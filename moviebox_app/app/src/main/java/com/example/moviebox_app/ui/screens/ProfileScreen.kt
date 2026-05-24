package com.example.moviebox_app.ui.screens

import androidx.compose.foundation.background
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
import androidx.compose.material.icons.outlined.Help
import androidx.compose.material.icons.outlined.Notifications
import androidx.compose.material.icons.outlined.Settings
import androidx.compose.material3.HorizontalDivider
import androidx.compose.material3.Icon
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.moviebox_app.ui.theme.Accent
import com.example.moviebox_app.ui.theme.Background
import com.example.moviebox_app.ui.theme.CardBg
import com.example.moviebox_app.ui.theme.DividerColor
import com.example.moviebox_app.ui.theme.TextMuted
import com.example.moviebox_app.ui.theme.TextPrimary
import com.example.moviebox_app.ui.theme.TextSecondary

@Composable
fun ProfileScreen() {
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

        // Avatar + info
        Row(verticalAlignment = Alignment.CenterVertically) {
            Box(
                modifier = Modifier
                    .size(64.dp)
                    .clip(CircleShape)
                    .background(Accent.copy(alpha = 0.2f)),
                contentAlignment = Alignment.Center,
            ) {
                Text("M", color = Accent, fontSize = 26.sp, fontWeight = FontWeight.ExtraBold)
            }

            Spacer(Modifier.width(16.dp))

            Column {
                Text("Mack Ronee", color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 17.sp)
                Text("mack@example.com", color = TextSecondary, fontSize = 13.sp)
            }
        }

        Spacer(Modifier.height(20.dp))

        // Subscription card
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .clip(RoundedCornerShape(14.dp))
                .background(Accent.copy(alpha = 0.12f))
                .padding(16.dp),
        ) {
            Column {
                Text("Active Plan", color = Accent, fontSize = 12.sp, fontWeight = FontWeight.SemiBold)
                Spacer(Modifier.height(4.dp))
                Text("Premium · KSH 899/mo", color = TextPrimary, fontSize = 15.sp, fontWeight = FontWeight.Bold)
                Spacer(Modifier.height(2.dp))
                Text("Renews June 24, 2026", color = TextMuted, fontSize = 12.sp)
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
        ProfileMenuItem(icon = Icons.Outlined.Help, label = "Help & Support")
        HorizontalDivider(color = DividerColor, thickness = 0.5.dp)
        ProfileMenuItem(icon = Icons.AutoMirrored.Outlined.Logout, label = "Sign Out", tint = Color(0xFFEF4444))

        Spacer(Modifier.height(32.dp))
    }
}

@Composable
private fun ProfileMenuItem(icon: ImageVector, label: String, tint: Color = TextSecondary) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 14.dp),
        verticalAlignment = Alignment.CenterVertically,
    ) {
        Icon(icon, null, tint = tint, modifier = Modifier.size(20.dp))
        Spacer(Modifier.width(14.dp))
        Text(label, color = if (tint == TextSecondary) TextPrimary else tint, fontSize = 15.sp)
    }
}
