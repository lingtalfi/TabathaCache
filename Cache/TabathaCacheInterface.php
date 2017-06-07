<?php


namespace TabathaCache\Cache;


/**
 *
 * Tabatha cache
 * ====================
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
 * Tabatha cache works at two levels.
 *
 * - the cache store/retrieving
 * - the cache cleaning
 *
 *
 * Cache store/retrieving
 * =========================
 *
 * This is the basic cache system: it uses the first two arguments of the "get" method: cacheId and generateCallback.
 * Basically, when you call the "get" method, one of two things can happen:
 *
 * - either the cache was already created, in which case the cached content is returned to you.
 * - either the cache was not already created, in which case we call the generateCallback callback, store its content for
 *          subsequent calls, and return the result to the caller.
 *
 *
 * That's a basic cache system.
 *
 *
 *
 * Cache cleaning
 * ==================
 *
 * This system is more specific, and requires some practice to get used to it, but I believe it's worth the training.
 * Basically, when you call the "get" method, you also set some delete namespaces (third argument of the get method).
 *
 * What's a delete namespace?
 *
 * It's a namespace that is used to clean the cache.
 * A delete namespace is triggered when you call the "clean" method with a delete id contained in that namespace (including the namespace itself).
 *
 * Let me illustrate that with some examples:
 *
 * todo
 *
 *
 * You can trigger a delete namespace by calling the
 *
 * So, when you call the "clean" method, you pass some delete ids to it.
 *
 * If the delete id is contained in one of the namespace,
 *
 *
 *
 *
 *
 *
 *
 *
 *
 * When you create a cache, you set not only a cache identifier, for instance myIdentifier,
 * but also one (or more) delete namespace(s), for instance "autumn".
 *
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
 * But even with that, we needed a more concise way to do it, and that's where the dotted notation comes in.
 *
 * In tabatha, you delete a cache by calling the clean method with a "delete identifier".
 *
 * The delete identifier uses
 *
 *
 * A component to the left of a dot is the parent of the component to the right.
 * So, basically with
 *
 *
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
     * @param $deleteNamespaces : string|array
     * @param $forceGenerate : null|bool=false, allows you to temporary force the
     *                          generateCallback (i.e. not using the cached version).
     *                          If null, takes the concrete instance's default value
     *
     *
     * @return mixed, the result of the generateCallback (or its cached equivalent if exist)
     */
    public function get($cacheId, callable $generateCallback, $deleteNamespaces, $forceGenerate = null);

    /**
     * Deletes the caches "listening" to the given deleteId(s).
     *
     * @param $deleteIds , string|array
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