TabathaCache
===========
2017-05-22



A cache system based on identifier invalidation.


This is part of the [universe framework](https://github.com/karayabin/universe-snapshot).


Install
==========
Using the [uni](https://github.com/lingtalfi/universe-naive-importer) command.
```bash
uni import TabathaCache
```

Or just download it and place it where you want otherwise.


How to
==========

Tabatha cache is a sexy cache system based on identifier invalidation.

Why sexy?

Because you can do a cache retrieving with one statement!

Often, other cache systems force you to make at least an if block (which makes things all the sudden appear
very complex, it gets out of the way of your application logic, it parasites your code), that's not the case
with tabatha cache: just one statement (see the example at the bottom of this comment for more details).

Enjoy!

This means when you create a cache, you set not only a cache identifier, for instance myIdentifier,
but also one (or more) delete identifier(s), for instance "autumn".

So with the cache identifier (myIdentifier), you can get the cached version of your content, as you would do
with a normal cache system.

But with your delete identifier (autumn), you can invalidate the myIdentifier cache, simply by using the clean method.

In other words, if you call this method:

- clean ( autumn ),

This will invalidate whatever cache has the autumn delete identifier.
It's like tags, if you will.

TabathaCache was first implemented to work with cache depending on some tables of a database.

And it's dotted namespace notation is inherited from there.

Understand that you can do multiple actions on a given table in a database, for instance create, update, delete.

So, if your table is named "customers", you could give a delete identifier of customers.update
to a cache, so that when some method triggers the "customers.update" delete identifier, your cache would be cleaned.

Ok, but what if you want the cache to be deleted not only on update, but also on create and delete?

That's why you can supply an array of delete identifiers to the clean method.

But even with that, we needed a more concise way to do it, and that's where the wildcard notation comes in.

Imagine how life would be simpler if you could just use the following delete identifier:

- customers.*

And this would represent any delete identifier which starts with "customers.".

Life would be great, and so we implemented it!

Now if you extend the system, depending on your organization you might have even more levels of depth.
For instance, you could have delete identifiers with more than one dot, like this:

- management.france.sectionA.phones

Well, the good news is that you can use wildcards at any level, so, all the following are valid and functional
delete identifiers, and they would do what you expect them to:

- *
- management.*
- management.france.*
- management.france.sectionA.*

However, the wildcard must be the last char.

Enjoy!

Example:
============
```php
$c = TabathaCache::create();

$c->clean('jacobe'); // call that when you want to clean the caches 

// create some cache values for the demo
a($c->get("pillustrator", function(){
    return "fuckingPillustrator";
}, "jacobe"));


a($c->get("photoslut", function(){
    return "fuckingPhotoslut";
}, "jacobe"));



//--------------------------------------------
// ANOTHER EXAMPLE FROM THE KAMILLE FRAMEWORK
//--------------------------------------------
$result = A::cache()->get('myCacheId', function () {
     // long operation
     $result = "resultOfLongOperation";
     return $result;
     }, [
         'ek_currency.create',
         'ek_currency.delete',
         'ek_currency.update',
         'ek_shop.*', // will be triggered by anything starting with "ek_shop."
 ]);
// now $result is available no matter what
// other dude create a record in the currency table
A::cache()->clean("ek_currency.create"); // this will remove the myCacheId entry




```




Cache strategies
===================

If you want to, you can refer to the following cache naming conventions in your projects.

Those conventions define how **delete identifiers** should be named.



Tabatha Db
--------------
This strategy is used when the cache is created by retrieving info from a database.

When this is the case, we can always identify from which table(s) the cache took its information from.

Those tables are the base name for the delete identifiers.

Imagine your application has a table named "category".

Now for a given table (like category for instance), we can execute different types of actions:

- create
- read
- update
- delete

The read action probably won't trigger a cache reset, but the other types potentially will.

So, the tabatha db convention is like this:

- deleteId: (<db> <.>)? <table> <.> <actionType>


You would use the db prefix if your application use multiple databases,
otherwise you can start directly with the table name.

The action type is one of: create, update, delete.

Remember that you can use the wild cards, so that:

- category.*

is equivalent to:

- category.create
- category.update
- category.delete














History Log
------------------    
    
- 1.3.0 -- 2017-05-23

    - add TabathaCacheInterface.cleanAll method
    
- 1.2.0 -- 2017-05-23

    - add TabathaCacheInterface.get $forceGenerate option
    
- 1.1.0 -- 2017-05-23

    - add debug hooks
    
- 1.0.0 -- 2017-05-22

    - initial commit