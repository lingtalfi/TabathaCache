TabathaCache
===========
2017-05-22


A tag-based cache system for your apps.


This is part of the [universe framework](https://github.com/karayabin/universe-snapshot).


Install
==========
Using the [uni](https://github.com/lingtalfi/universe-naive-importer) command.
```bash
uni import TabathaCache
```

Or just download it and place it where you want otherwise.





Welcome to tabatha cache 2.
===================
If you want to see the old tabathaCache (version 1), please browse the README.2018-06-08.md file in this repository.

But for now, let's forget everything you know about tabatha cache, and let's start a brand new tool.

TabathaCache2 is a tag-based cache system for your apps.

Tag based means you can attach tags to your cached entries,
and then later you can delete those cache entries just by using the tag identifiers.


TLDR;
----------------
This class uses a base dir with the following structure:

- $baseDir/:
     - cached_entries/
         - $cacheIdentifier.cache.php
         - ...
     - delete_ids/
         - $deleteId.list.php
         - ...

The "cache_entries" dir contains the cached content.
The "delete_ids" dir contains the list of cacheIdentifiers to delete if the $deleteId is given to the clean method.






Crash course
--------------------
The first method to learn is "get".

The "get" method creates a cache entry if necessary (i.e. if the content
you're asking has not been cached yet), and then returns the cached content.


Here is how we use it:

```php
// assuming $cache is a well configured TabathaCache2 instance
$myCachedContent = $cache->get( "theCacheIdentifier", function(){
     return "some very long string";
});
```


With the code above, the first call will trigger the callback, cache it somewhere, and returns its output.
All subsequent calls will return the cached content.


That's fine, but in this example we didn't attach any tag to this cached entry, and so programmatically
speaking we don't have a way to erase our cached content.

It's not too hard to add tags to a cache content though, just look at the code below, which does exactly that:


```php
$myCachedContent = $cache->get( "theCacheIdentifier", function(){
     return "some very long string";
}, ["myDeleteId"]);
```


See how easy it was?
This leads us to the second part: deleting cache content programmatically.


Continuing the above example, let's say that now I want to delete cache entry which identifier is theCacheIdentifier.
Since I've assigned the myDeleteId tag in the very last snippet, I can just use that tag now, like this:

```php
$cache->clean(["myDeleteId"]);
```



Ok, that's it for this tutorial.
I'm sure you get the idea.





History Log
------------------    
    
- 2.0.0 -- 2018-06-08

    - add TabathaCache2 class
    
- 1.5.0 -- 2017-06-08

    - wildcard system is now implicit
    
- 1.4.1 -- 2017-05-24

    - fix TabathaCacheInterface.clean method, deleteIds are now processed
    
- 1.4.0 -- 2017-05-24

    - add TabathaCacheInterface.setDefaultForceGenerate method
    
- 1.3.0 -- 2017-05-23

    - add TabathaCacheInterface.cleanAll method
    
- 1.2.0 -- 2017-05-23

    - add TabathaCacheInterface.get $forceGenerate option
    
- 1.1.0 -- 2017-05-23

    - add debug hooks
    
- 1.0.0 -- 2017-05-22

    - initial commit