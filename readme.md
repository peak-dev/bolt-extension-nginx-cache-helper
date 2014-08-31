NginX Cache Helper
==================

A simple application that helps to flush a record's content from Nginx FastCGI, 
proxy, SCGI and uWSGI caches on the record's post-save event.

This requires that you are running NginX with the [ngx_cache_purge](http://labs.frickle.com/nginx_ngx_cache_purge/) 
module compiled in.

Set up
------

You will need to add a location block to your Bolt `server {}` configuration,
similar to:

```
server {
    
    <The rest of your server block>

    location ~ /purge(/.*) {
        allow              127.0.0.1;
        deny               all;
        proxy_cache_purge  tmpcache $1$is_args$args;
    }
}
```

The URI match used above, `purge` here, is the value you need to match in your
extension configuration file's `nginx_purge_uri:` paramter.

For more details on setting up ngx_cache_purge, see:
    https://github.com/FRiCKLE/ngx_cache_purge/blob/master/README.md