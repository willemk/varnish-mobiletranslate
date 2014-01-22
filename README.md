varnish Mobile Detect
=======================

###Goal:
Drop-in varnish solution to mobile user detection based on the https://github.com/serbanghita/Mobile-Detect library.

###How:

A VCL script that adds a `X-UA-Device` header to the request.

Device Type|X-UA-Device
-----------|------------
Phone      |  mobile
Tablet     |  mobile;tablet
Other      |  desktop

###Requirements

Varnish 3 is a requirement. If you venture running this with Varnish 2, there is no gurantee for support. 

It is also helpful to know understand the basics of the VCL language and workflow. 

This is not a VMOD module, so no compiling is required. 

###Installation

1. Download the mobile_detect.vcl file into your vcl config directory. `/etc/varnish/`
 * `wget https://raw2.github.com/willemk/varnish-mobiletranslate/master/mobile_detect.vcl`
2. Include the `mobile_detect.vcl` in your `default.vcl` file (See examples below)
3. Call `devicedetect` from vcl_recv in varnish (See examples below)

###Usage

####Sample use case: Different back ends

Using a different backend for mobile than for desktop 


```
include "mobile_detect.vcl";

backend mobile {
    .host = "10.0.0.1";
    .port = "80";
}

sub vcl_recv {

    call devicedetect;

    if (req.http.X-UA-Device ~ "^mobile") {
        set req.backend = mobile;
    }
}

```
####Sample use case: Normalize the user-agent string for the backend


```
include "mobile_detect.vcl";

sub vcl_recv {

    call devicedetect;

    if (req.http.X-UA-Device) {
        set req.http.User-Agent = req.http.X-UA-Device;
        unset req.http.X-UA-Device;
    }
}

```

####Sample use case: Cache based on device type


```
include "mobile_detect.vcl";

sub vcl_recv {

    call devicedetect;

}

sub vcl_hash {
    #Default Hash
    hash_data(req.url);
    if (req.http.host) {
        hash_data(req.http.host);
    } else {
        hash_data(server.ip);
    }
    
    #Also hash based on device type
    if (req.http.X-UA-Device) {
        hash_data(req.http.X-UA-Device);
    }
    
    return (hash);
}

```

###Bugs & Features

If you have any  [bug reports](#bugs), [features requests](#features) or want to [submit a pull requests](#pull-requests), please use the appropriate github tools. 


###Thanks

Thanks to these projects for help and inspiration

https://github.com/serbanghita/Mobile-Detect

https://github.com/varnish/varnish-devicedetect
