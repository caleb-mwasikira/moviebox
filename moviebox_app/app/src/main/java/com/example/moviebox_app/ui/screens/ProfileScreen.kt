package com.example.moviebox_app.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.outlined.Logout
import androidx.compose.material.icons.outlined.AccountCircle
import androidx.compose.material.icons.outlined.CreditCard
import androidx.compose.material.icons.automirrored.outlined.Help
import androidx.compose.material.icons.outlined.Notifications
import androidx.compose.material.icons.outlined.Settings
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.HorizontalDivider
import androidx.compose.material3.Icon
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.moviebox_app.data.UiState
import com.example.moviebox_app.data.auth.UserProfile
import com.example.moviebox_app.ui.theme.Accent
import com.example.moviebox_app.ui.theme.Background
import com.example.moviebox_app.ui.theme.CardBg
import com.example.moviebox_app.ui.theme.DividerColor
import com.example.moviebox_app.ui.theme.TextMuted
import com.example.moviebox_app.ui.theme.TextPrimary
import com.example.moviebox_app.ui.theme.TextSecondary
import com.example.moviebox_app.ui.viewmodel.ProfileViewModel

@Composable
fun ProfileScreen(
    onSignOut: () -> Unit,
    vm: ProfileViewModel = viewModel(),
) {
    val state by vm.state.collectAsState()

    Box(modifier = Modifier.fillMaxSize().background(Background)) {
        when (val s = state) {
            is UiState.Loading -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = Accent)
            }

            is UiState.Error -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                Text(s.message, color = TextSecondary, fontSize = 14.sp)
            }

            is UiState.Success -> ProfileContent(
                profile  = s.data,
                onSignOut = {
                    vm.logout(onDone = onSignOut)
                },
            )
        }
    }
}

@Composable
private fun ProfileContent(profile: UserProfile, onSignOut: () -> Unit) {
    val initial = profile.name.firstOrNull()?.uppercaseChar()?.toString() ?: "U"

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Background)
            .verticalScroll(rememberScrollState())
            .padding(horizontal = 16.dp),
    ) {
        Spacer(Modifier.height(24.dp))

        Text("Profile", color = TextPrimary, fontWeight = FontWeight.ExtraBold, fontSize = 22.sp)

        Spacer(Modifier.height(28.dp))

        Row(verticalAlignment = Alignment.CenterVertically) {
            Box(
                modifier = Modifier
                    .size(64.dp)
                    .clip(CircleShape)
                    .background(Accent.copy(alpha = 0.2f)),
                contentAlignment = Alignment.Center,
            ) {
                Text(initial, color = Accent, fontSize = 26.sp, fontWeight = FontWeight.ExtraBold)
            }

            Spacer(Modifier.width(16.dp))

            Column {
                Text(profile.name, color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 17.sp)
                Text(profile.email, color = TextSecondary, fontSize = 13.sp)
            }
        }

        Spacer(Modifier.height(20.dp))

        val sub = profile.subscription
        if (sub != null) {
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .clip(RoundedCornerShape(14.dp))
                    .background(Accent.copy(alpha = 0.12f))
                    .padding(16.dp),
            ) {
                Column {
                    val statusLabel = when (sub.status.lowercase()) {
                        "active" -> "Active Plan"
                        "expired" -> "Expired Plan"
                        else -> sub.status.replaceFirstChar { it.uppercase() }
                    }
                    Text(statusLabel, color = Accent, fontSize = 12.sp, fontWeight = FontWeight.SemiBold)
                    Spacer(Modifier.height(4.dp))
                    Text(sub.`package` ?: "Premium", color = TextPrimary, fontSize = 15.sp, fontWeight = FontWeight.Bold)
                    if (sub.expiresAt != null) {
                        Spacer(Modifier.height(2.dp))
                        val label = if (sub.status.lowercase() == "expired") "Expired" else "Renews"
                        Text("$label ${formatExpiry(sub.expiresAt)}", color = TextMuted, fontSize = 12.sp)
                    }
                }
            }
        } else {
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .clip(RoundedCornerShape(14.dp))
                    .background(CardBg)
                    .padding(16.dp),
            ) {
                Text("No active subscription", color = TextMuted, fontSize = 14.sp)
            }
        }

        Spacer(Modifier.height(28.dp))

        Text("Account", color = TextMuted, fontSize = 12.sp, fontWeight = FontWeight.SemiBold, modifier = Modifier.padding(bottom = 8.dp))

        ProfileMenuItem(icon = Icons.Outlined.AccountCircle, label = "Edit Profile")
        HorizontalDivider(color = DividerColor, thickness = 0.5.dp)
        ProfileMenuItem(icon = Icons.Outlined.CreditCard, label = "Manage Subscription")
        HorizontalDivider(color = DividerColor, thickness = 0.5.dp)
        ProfileMenuItem(icon = Icons.Outlined.Notifications, label = "Notifications")

        Spacer(Modifier.height(24.dp))

        Text("App", color = TextMuted, fontSize = 12.sp, fontWeight = FontWeight.SemiBold, modifier = Modifier.padding(bottom = 8.dp))

        ProfileMenuItem(icon = Icons.Outlined.Settings, label = "Settings")
        HorizontalDivider(color = DividerColor, thickness = 0.5.dp)
        ProfileMenuItem(icon = Icons.AutoMirrored.Outlined.Help, label = "Help & Support")
        HorizontalDivider(color = DividerColor, thickness = 0.5.dp)
        ProfileMenuItem(
            icon = Icons.AutoMirrored.Outlined.Logout,
            label = "Sign Out",
            tint = Color(0xFFEF4444),
            onClick = onSignOut,
        )

        Spacer(Modifier.height(32.dp))
    }
}

@Composable
private fun ProfileMenuItem(
    icon: ImageVector,
    label: String,
    tint: Color = TextSecondary,
    onClick: () -> Unit = {},
) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .clickable(onClick = onClick)
            .padding(vertical = 14.dp),
        verticalAlignment = Alignment.CenterVertically,
    ) {
        Icon(icon, null, tint = tint, modifier = Modifier.size(20.dp))
        Spacer(Modifier.width(14.dp))
        Text(label, color = if (tint == TextSecondary) TextPrimary else tint, fontSize = 15.sp)
    }
}

private fun formatExpiry(iso: String): String {
    return try {
        val parts = iso.substringBefore('T').split('-')
        val months = listOf("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec")
        val month = parts[1].toInt() - 1
        "${parts[2].toInt()} ${months[month]}, ${parts[0]}"
    } catch (_: Exception) { iso }
}
