import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \app\http\Controllers\FeeController::create
 * @see [unknown]:0
 * @route '/fees/generate'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/fees/generate',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \app\http\Controllers\FeeController::create
 * @see [unknown]:0
 * @route '/fees/generate'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \app\http\Controllers\FeeController::create
 * @see [unknown]:0
 * @route '/fees/generate'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \app\http\Controllers\FeeController::create
 * @see [unknown]:0
 * @route '/fees/generate'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

    /**
* @see \app\http\Controllers\FeeController::create
 * @see [unknown]:0
 * @route '/fees/generate'
 */
    const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: create.url(options),
        method: 'get',
    })

            /**
* @see \app\http\Controllers\FeeController::create
 * @see [unknown]:0
 * @route '/fees/generate'
 */
        createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url(options),
            method: 'get',
        })
            /**
* @see \app\http\Controllers\FeeController::create
 * @see [unknown]:0
 * @route '/fees/generate'
 */
        createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: create.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    create.form = createForm
/**
* @see \app\http\Controllers\FeeController::store
 * @see [unknown]:0
 * @route '/fees/store'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/fees/store',
} satisfies RouteDefinition<["post"]>

/**
* @see \app\http\Controllers\FeeController::store
 * @see [unknown]:0
 * @route '/fees/store'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \app\http\Controllers\FeeController::store
 * @see [unknown]:0
 * @route '/fees/store'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \app\http\Controllers\FeeController::store
 * @see [unknown]:0
 * @route '/fees/store'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \app\http\Controllers\FeeController::store
 * @see [unknown]:0
 * @route '/fees/store'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
const FeeController = { create, store }

export default FeeController