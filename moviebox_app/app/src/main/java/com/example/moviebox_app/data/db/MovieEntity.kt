package com.example.moviebox_app.data.db

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "movies")
data class MovieEntity(
    @PrimaryKey val uuid: String,
    val title: String,
    val description: String?,
    val genre: String?,
    val year: Int?,
    val duration: Int?,
    val isFree: Boolean,
    val featured: Boolean,
    val thumbnailUrl: String?,
    val hasStream: Boolean,
    val streamingUrl: String?,
    val trailerUrl: String?,
    val createdAt: String?,
)
