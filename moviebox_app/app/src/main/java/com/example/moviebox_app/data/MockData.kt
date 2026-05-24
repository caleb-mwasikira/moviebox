package com.example.moviebox_app.data

import androidx.compose.ui.graphics.Color

data class Movie(
    val id: Int,
    val title: String,
    val year: Int,
    val duration: String,
    val rating: String,
    val genre: List<String>,
    val description: String,
    val matchPercent: Int,
    val isFeatured: Boolean = false,
    val posterColor: Color = Color(0xFF1A1A2E),
    val posterAccent: Color = Color(0xFFF5C518),
    val cast: List<String> = emptyList(),
)

data class Series(
    val id: Int,
    val title: String,
    val year: Int,
    val seasons: Int,
    val genre: List<String>,
    val description: String,
    val posterColor: Color = Color(0xFF1A2E1A),
    val posterAccent: Color = Color(0xFF46D369),
)

object MockData {
    val featured = Movie(
        id = 1,
        title = "John Wick\nChapter 4",
        year = 2023,
        duration = "2h 49m",
        rating = "R",
        genre = listOf("Action", "Thriller"),
        description = "John Wick uncovers a path to defeating The High Table. But before he can earn his freedom, Wick must face off against a new enemy with powerful alliances across the globe and forces that turn old friends into foes.",
        matchPercent = 95,
        isFeatured = true,
        posterColor = Color(0xFF2C1810),
        posterAccent = Color(0xFFF5C518),
        cast = listOf("Keanu Reeves", "Laurance Fi...", "George Geo...", "Bill S"),
    )

    val trending = listOf(
        Movie(
            id = 2, title = "The Glory", year = 2023, duration = "1h 2m", rating = "TV-MA",
            genre = listOf("Drama", "Thriller"),
            description = "A woman who suffered brutal school violence plans revenge against her tormentors.",
            matchPercent = 92,
            posterColor = Color(0xFF1A0A2E),
            posterAccent = Color(0xFF9B59B6),
        ),
        Movie(
            id = 3, title = "Arcane", year = 2021, duration = "40m", rating = "TV-14",
            genre = listOf("Animation", "Action", "Adventure"),
            description = "Amid the conflicts between the cities of Piltover and Zaun, two sisters fight on opposite sides of a war.",
            matchPercent = 97,
            posterColor = Color(0xFF0A1A2E),
            posterAccent = Color(0xFF3498DB),
        ),
        Movie(
            id = 4, title = "Peaky Blinders", year = 2013, duration = "1h", rating = "TV-MA",
            genre = listOf("Crime", "Drama"),
            description = "A gangster family epic set in 1900s England, centering on a gang who sew razor blades in the peaks of their caps.",
            matchPercent = 89,
            posterColor = Color(0xFF1A1208),
            posterAccent = Color(0xFFE67E22),
        ),
        Movie(
            id = 5, title = "Oppenheimer", year = 2023, duration = "3h", rating = "R",
            genre = listOf("Biography", "Drama", "History"),
            description = "The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb.",
            matchPercent = 94,
            posterColor = Color(0xFF1A0A0A),
            posterAccent = Color(0xFFE74C3C),
        ),
        Movie(
            id = 6, title = "Dune Part 2", year = 2024, duration = "2h 46m", rating = "PG-13",
            genre = listOf("Sci-Fi", "Adventure"),
            description = "Paul Atreides unites with Chani and the Fremen while seeking revenge against the conspirators who destroyed his family.",
            matchPercent = 91,
            posterColor = Color(0xFF2E2010),
            posterAccent = Color(0xFFD4A017),
        ),
    )

    val nowShowing = listOf(
        Movie(
            id = 7, title = "Scorpio", year = 2024, duration = "1h 24m", rating = "PG-13",
            genre = listOf("Action", "Thriller"),
            description = "An elite operative is forced out of retirement when the organization he built turns against him.",
            matchPercent = 78,
            posterColor = Color(0xFF0A1A0A),
            posterAccent = Color(0xFF27AE60),
        ),
        Movie(
            id = 8, title = "Avatar 2", year = 2022, duration = "3h 12m", rating = "PG-13",
            genre = listOf("Sci-Fi", "Adventure"),
            description = "Jake Sully lives with his newfound family formed on the planet of Pandora.",
            matchPercent = 82,
            posterColor = Color(0xFF051A2E),
            posterAccent = Color(0xFF1ABC9C),
        ),
        Movie(
            id = 9, title = "Anaconda", year = 2024, duration = "1h 35m", rating = "PG-13",
            genre = listOf("Action", "Adventure", "Horror"),
            description = "A group of explorers encounter a deadly giant anaconda deep in the Amazon.",
            matchPercent = 65,
            posterColor = Color(0xFF0A1A05),
            posterAccent = Color(0xFF2ECC71),
        ),
    )

    val popular = listOf(
        Movie(
            id = 10, title = "Scary Movie", year = 2000, duration = "1h 24m", rating = "R",
            genre = listOf("Comedy", "Horror", "Satire"),
            description = "A group of teenagers who accidentally hit a man with their car and dump his body, only to have an unknown killer start stalking them.",
            matchPercent = 75,
            posterColor = Color(0xFF1A0A1A),
            posterAccent = Color(0xFF8E44AD),
        ),
        Movie(
            id = 11, title = "Just Mercy", year = 2019, duration = "1h 37m", rating = "PG-13",
            genre = listOf("Drama", "Biography", "Crime"),
            description = "A Harvard-educated lawyer returns to Alabama, where he is determined to defend those wrongly condemned.",
            matchPercent = 88,
            posterColor = Color(0xFF0A0A1A),
            posterAccent = Color(0xFF2980B9),
        ),
        Movie(
            id = 12, title = "Out of the Furnace 2", year = 2024, duration = "2h 5m", rating = "R",
            genre = listOf("Crime", "Drama", "Thriller"),
            description = "A steelworker's brother goes missing, setting him on a collision course with a ruthless crime boss.",
            matchPercent = 71,
            posterColor = Color(0xFF1A0A05),
            posterAccent = Color(0xFFE67E22),
        ),
    )

    val seriesList = listOf(
        Series(
            id = 1, title = "Breaking Bad", year = 2008, seasons = 5,
            genre = listOf("Crime", "Drama", "Thriller"),
            description = "A high school chemistry teacher diagnosed with cancer turns to manufacturing and selling methamphetamine.",
            posterColor = Color(0xFF1A1A08),
            posterAccent = Color(0xFFF39C12),
        ),
        Series(
            id = 2, title = "YOU", year = 2018, seasons = 4,
            genre = listOf("Thriller", "Drama"),
            description = "A bookstore manager uses the internet and social media to stalk and obsess over his desired partner.",
            posterColor = Color(0xFF0A0A1A),
            posterAccent = Color(0xFF3498DB),
        ),
        Series(
            id = 3, title = "Narcos", year = 2015, seasons = 3,
            genre = listOf("Biography", "Crime", "Drama"),
            description = "A chronicled look at the criminal exploits of Colombian drug lord Pablo Escobar.",
            posterColor = Color(0xFF1A0A08),
            posterAccent = Color(0xFFE74C3C),
        ),
        Series(
            id = 4, title = "Stranger Things", year = 2016, seasons = 4,
            genre = listOf("Horror", "Sci-Fi", "Drama"),
            description = "A group of kids uncover a government conspiracy involving secret experiments and supernatural forces.",
            posterColor = Color(0xFF08101A),
            posterAccent = Color(0xFF9B59B6),
        ),
    )

    fun findMovie(id: Int): Movie? = (listOf(featured) + trending + nowShowing + popular).find { it.id == id }
    fun findSeries(id: Int): Series? = seriesList.find { it.id == id }
}
