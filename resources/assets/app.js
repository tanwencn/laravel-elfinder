var Finder = function (c) {
    this.key = Math.floor(Math.random() * (999999 - 100000)) + 100000;
    Finder.instances[this.key] = this;
    var container;
    var completeCall = null;

    var config = {
        title: "",
        url: "",
        query: {
            j_instance_key:this.key,
            disks: ['default'],
            multiple: false
        }
    };
    $.extend(config, Finder.default);

    this.config = function (c) {
        $.each(c, function (key, val) {
            if ($.inArray(key, ["title", "url"]) >= 0) {
                config[key] = val;
            } else {
                config.query[key] = val;
            }
        });
        return this;
    };

    if (c != undefined)
        this.config(c);

    var event_el = null;
    this.click = function (el, call) {
        event_el = $(el);
        var self = this;
        if (typeof call !== 'function') {
            var selector = call;
            call = function (file) {
                $(selector).val(file.url)
            };
        }

        event_el.on('click', function () {
            self.success(call).open();
        });
    };
    this.success = function (call) {
        completeCall = call;
        return this;
    };
    this.getUrl = function () {
        if (config.url.indexOf("?") != -1)
            return config.url + "&" + Admin.query(config.query);
        else
            return config.url + "?" + Admin.query(config.query);
    };
    this.open = function () {
        container = $.dialog({
            title: config.title,
            content: "URL:" + this.getUrl(),
            animation: 'scale',
            closeAnimation: 'scale',
            backgroundDismiss: true,
            columnClass: 'xlarge',
        });
        return this;
    };
    this.close = function (file) {
        completeCall(file, event_el);
        container.close();
    };
};
Finder.instances = {};
Finder.default = {};
Finder.instance = function(key){
    return Finder.instances[key];
};
Finder.disk = function (disk, c) {
    if(typeof c != 'object') c = {};
    if(disk != undefined && disk != null) {
        if (!(disk instanceof Array || disk instanceof Object))
            c.disk = [disk];
        else
            c.disk = [disk];
    }

    return new Finder(c);
};
