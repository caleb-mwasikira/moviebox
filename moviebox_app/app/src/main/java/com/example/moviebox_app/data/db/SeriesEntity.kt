package com.example.moviebox_app.data.db

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "series")
data class SeriesEntity(
    @PrimaryKey val uuid: String,
    val title: String,
    val description: String?,
    val genre: String?,
    val year: Int?,
    val featured: Boolean,
    val comingSoon: Boolean,
    val thumbnailUrl: String?,
    val trailerUrl: String?,
    val createdAt: String?,
)
