package com.example.moviebox_app.data.api

class ApiRepository(private val service: ApiService = ApiClient.service) {

    suspend fun search(query: String? = null, genre: String? = null): SearchResult =
        service.search(query?.takeIf { it.isNotBlank() }, genre?.takeIf { it != "All" })

    suspend fun getMovies(
        page: Int = 1,
        query: String? = null,
        genre: String? = null,
    ): List<ApiMovie> = service.getMovies(page, query?.takeIf { it.isNotBlank() }, genre?.takeIf { it != "All" }).data

    suspend fun getMovie(uuid: String): ApiMovie =
        service.getMovie(uuid).data

    suspend fun getSeries(page: Int = 1): List<ApiSeries> =
        service.getSeries(page).data

    suspend fun getSeriesDetail(uuid: String): ApiSeries =
        service.getSeriesDetail(uuid).data

    suspend fun getSubscriptionPackages(): List<ApiSubscriptionPackage> =
        service.getSubscriptionPackages().data

    suspend fun getCurrentSubscription(): ApiCurrentSubscription? =
        service.getCurrentSubscription().data

    suspend fun initiateSubscription(phone: String, packageUuid: String): SubscriptionInitiateResponse =
        service.initiateSubscription(SubscriptionInitiateRequest(phone, packageUuid))

    suspend fun getSubscriptionStatus(checkoutRequestId: String): SubscriptionStatusResponse =
        service.getSubscriptionStatus(checkoutRequestId)
}
