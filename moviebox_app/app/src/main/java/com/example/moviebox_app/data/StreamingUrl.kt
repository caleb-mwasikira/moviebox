package com.example.moviebox_app.data

import com.example.moviebox_app.BuildConfig
import java.util.Base64
import javax.crypto.Mac
import javax.crypto.spec.SecretKeySpec

fun resolveStreamingUrl(path: String?): String? {
    if (path.isNullOrBlank()) return null
    if (path.startsWith("http")) return path
    val base = BuildConfig.STREAMING_BASE_URL.trimEnd('/')
    return base + "/" + path.trimStart('/')
}

// Produces a streaming bearer token matching streamAuth in the Go service:
// base64(uid:exp:hmac_hex) where hmac = HMAC-SHA256(uid:exp, APP_SECRET)
fun buildStreamingToken(userId: Int): String {
    val exp = System.currentTimeMillis() / 1000 + 6 * 3600   // 6-hour window
    val message = "$userId:$exp"
    val mac = Mac.getInstance("HmacSHA256")
    mac.init(SecretKeySpec(BuildConfig.STREAMING_SECRET.toByteArray(Charsets.UTF_8), "HmacSHA256"))
    val sigHex = mac.doFinal(message.toByteArray(Charsets.UTF_8))
        .joinToString("") { "%02x".format(it) }
    return Base64.getEncoder().encodeToString("$message:$sigHex".toByteArray(Charsets.UTF_8))
}
