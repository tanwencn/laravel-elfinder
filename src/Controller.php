<?php
namespace Tanwencn\Elfinder;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;
use Tanwencn\Elfinder\Interfaces\FinderAuth;
use Tanwencn\Elfinder\Interfaces\FinderOption;

class Controller extends BaseController
{
    protected $package = 'elfinder';

    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    protected $session;

    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->session = new LaravelSession(app('session.store'));
    }

    public function showIndex(Request $request)
    {
        $this->setDisks($request->input('disks', []));

        return view('elfinder::standalonepopup')->with($this->getViewVars());
    }

    protected function mergeRootOption($option, $disk){
        $disk->makeDirectory($option['path']);
        if(!empty($option['onlyMimes']) && !isset($option['uploadAllow'])){
            $option['uploadAllow'] = $option['onlyMimes'];
        }
        if(empty($option['URL'])){
            $option['URL'] = $disk->url($option['path']);
        }
        if(empty($option['tmbPath'])) {
            $option['tmbPath'] = Str::finish($disk->path($option['path']), DIRECTORY_SEPARATOR).'.tmb';
            $option['tmbURL'] = Str::finish($disk->url($option['path']), DIRECTORY_SEPARATOR).'.tmb';
        }

        $option['attributes'][] = [
            'pattern' => '/'. basename($option['tmbPath']) .'/',
            'hidden'    => true
        ];

        return $option;
    }

    protected function getRoots(){
        $roots = [];
        $disks = $this->getDisks();
        foreach ((array)$disks as $key) {
            $config = config("elfinder.roots.{$key}");
            $options = $config['options'];
            $disk = app('filesystem')->disk($options['disk']);
            if(isset($config['process'])){
                $process = new $config['process'];
                if($process instanceof FinderAuth  && !$process->auth())
                    continue;
                if($process instanceof FinderOption)
                    $options = $process->option($options);
            }
            if ($disk instanceof FilesystemAdapter) {
                $roots[$key] = array_merge($this->mergeRootOption($options, $disk), [
                    'driver' => 'Flysystem',
                    'filesystem' => $disk->getDriver(),
                    'alias' => isset($options['alias']) ? $options['alias'] : $key,
                    'admin_key' => $key,
                    'accessControl'=>'access'
                ]);
            }
        }

        return $roots;
    }

    public function showConnector()
    {
        $opts = array_merge(config('elfinder.option', []), [
            'roots' => array_values($this->getRoots()),
            'session' => $this->session
        ]);

        // run elFinder
        $connector = new Connector(new ElFinder($opts));
        $connector->run();
        return $connector->getResponse();
    }

    protected function setDisks($disks)
    {
        $this->session->set('admin_elfinder_disks', $disks);
    }

    protected function getDisks()
    {
        $filters = [];
        $disks = $this->session->get('admin_elfinder_disks');
        $config = config("elfinder.roots", []);
        foreach ((array)$disks as $key) {
            if ($key && isset($config[$key]))
                $filters[] = $key;
        }
        if (empty($filters)) $filters[] = 'default';

        return array_unique($filters);
    }

    protected function getViewVars()
    {
        $arr = explode('-', $this->app->config->get('app.locale'));
        if (isset($arr[1])) $arr[1] = strtoupper($arr[1]);
        $locale = implode('_', $arr);

        $multiple = request('multiple', 0);
        $multiple = ($multiple == 'false' || !$multiple) ? 0 : 1;

        $is_tree = count($this->getDisks()) > 1;

        if(empty($this->getRoots())){
            if(config('app.debug'))
                abort(403, "no disks");
            else
                abort(404);
        }

        return compact('locale', 'is_tree', 'multiple');
    }
}
