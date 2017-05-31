<?php


namespace TabathaCache\Cache;


/**
 *
 *
 * Tabatha cache is a sexy cache system based on identifier invalidation.
 *
 * Why sexy?
 * Because you can do a cache retrieving with one statement!
 * Often, other cache systems force you to make at least an if block (which makes things all the sudden appear
 * very complex, it gets out of the way of your application logic, it parasites your code), that's not the case
 * with tabatha cache: just one statement (see the example at the bottom of this comment for more details).
 * Enjoy!
 *
 *
 *
 *
 * This means when you create a cache, you set not only a cache identifier, for instance myIdentifier,
 * but also one (or more) delete identifier(s), for instance "autumn".
 *
 * So with the cache identifier (myIdentifier), you can get the cached version of your content, as you would do
 * with a normal cache system.
 *
 * But with your delete identifier (autumn), you can invalidate the myIdentifier cache, simply by using the clean method.
 *
 * In other words, if you call this method:
 *
 * - clean ( autumn ),
 *
 * This will invalidate whatever cache has the autumn delete identifier.
 * It's like tags, if you will.
 *
 *
 * TabathaCache was first implemented to work with cache depending on some tables of a database.
 * And it's dotted namespace notation is inherited from there.
 *
 * Understand that you can do multiple actions on a given table in a database, for instance create, update, delete.
 * So, if your table is named "customers", you could give a delete identifier of customers.update
 * to a cache, so that when some method triggers the "customers.update" delete identifier, your cache would be cleaned.
 *
 * Ok, but what if you want the cache to be deleted not only on update, but also on create and delete?
 * That's why you can supply an array of delete identifiers to the clean method.
 *
 * But even with that, we needed a more concise way to do it, and that's where the wildcard notation comes in.
 *
 * Imagine how life would be simpler if you could just use the following delete identifier:
 *
 * - customers.*
 *
 * And this would represent any delete identifier which starts with "customers.".
 *
 * Life would be great, and so we implemented it!
 *
 *
 * Now if you extend the system, depending on your organization you might have even more levels of depth.
 * For instance, you could have delete identifiers with more than one dot, like this:
 *
 *
 * - management.france.sectionA.phones
 *
 * Well, the good news is that you can use wildcards at any level, so, all the following are valid and functional
 * delete identifiers, and they would do what you expect them to:
 *
 * - *
 * - management.*
 * - management.france.*
 * - management.france.sectionA.*
 *
 * However, the wildcard must be the last char.
 *
 *
 * Note: for a db system, tabatha can help you implementing a cache system that operates at the entry/row level.
 * Just add the primary keys, in your delete id:
 *
 * - my_table.delete.6      # we can store a cache that would be only deleted if the record #6 is deleted from my_table for instance
 *
 *
 *
 * Enjoy!
 *
 *
 *
 * Example:
 * ============
 * $result = A::cache()->get('myCacheId', function () {
 *      // long operation
 *      $result = "resultOfLongOperation";
 *      return $result;
 *      }, [
 *          'ek_currency.create',
 *          'ek_currency.delete',
 *          'ek_currency.update',
 *          'ek_shop.*', // will be triggered by anything starting with "ek_shop."
 *  ]);
 * // now $result is available no matter what
 *
 *
 * // other dude create a record in the currency table
 * A::cache()->clean("ek_currency.create"); // this will remove the myCacheId entry
 *
 *
 */
interface TabathaCacheInterface
{

    /**
     * @param $cacheId , string, the cache identifier.
     * @param callable $generateCallback , creates the result: is called only if the cache doesn't exist
     * @param $deleteIds : string|array
     * @param $forceGenerate : null|bool=false, allows you to temporary force the
     *                          generateCallback (i.e. not using the cached version).
     *                          If null, takes the concrete instance's default value
     *
     *
     * @return mixed, the result of the generateCallback (or its cached equivalent if exist)
     */
    public function get($cacheId, callable $generateCallback, $deleteIds, $forceGenerate=null);

    /**
     * Deletes the caches "listening" to the given deleteId(s).
     *
     * @param $deleteIds, string|array
     * @return void
     */
    public function clean($deleteIds);

    /**
     * Cleans all caches.
     *
     * @return void
     */
    public function cleanAll();
}