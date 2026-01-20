import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\SchoolClassController::index
 * @see app/Http/Controllers/SchoolClassController.php:11
 * @route '/classes'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/classes',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SchoolClassController::index
 * @see app/Http/Controllers/SchoolClassController.php:11
 * @route '/classes'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SchoolClassController::index
 * @see app/Http/Controllers/SchoolClassController.php:11
 * @route '/classes'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\SchoolClassController::index
 * @see app/Http/Controllers/SchoolClassController.php:11
 * @route '/classes'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\SchoolClassController::index
 * @see app/Http/Controllers/SchoolClassController.php:11
 * @route '/classes'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\SchoolClassController::index
 * @see app/Http/Controllers/SchoolClassController.php:11
 * @route '/classes'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\SchoolClassController::index
 * @see app/Http/Controllers/SchoolClassController.php:11
 * @route '/classes'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\SchoolClassController::store
 * @see app/Http/Controllers/SchoolClassController.php:18
 * @route '/classes/store'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/classes/store',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SchoolClassController::store
 * @see app/Http/Controllers/SchoolClassController.php:18
 * @route '/classes/store'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SchoolClassController::store
 * @see app/Http/Controllers/SchoolClassController.php:18
 * @route '/classes/store'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\SchoolClassController::store
 * @see app/Http/Controllers/SchoolClassController.php:18
 * @route '/classes/store'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\SchoolClassController::store
 * @see app/Http/Controllers/SchoolClassController.php:18
 * @route '/classes/store'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
const SchoolClassController = { index, store }

export default SchoolClassController