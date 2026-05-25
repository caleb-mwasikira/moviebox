package com.example.moviebox_app.data.db

import androidx.room.Database
import androidx.room.RoomDatabase

@Database(
    entities = [MovieEntity::class, SeriesEntity::class],
    version  = 1,
    exportSchema = false,
)
abstract class AppDatabase : RoomDatabase() {
    abstract fun movieDao(): MovieDao
    abstract fun seriesDao(): SeriesDao
}
