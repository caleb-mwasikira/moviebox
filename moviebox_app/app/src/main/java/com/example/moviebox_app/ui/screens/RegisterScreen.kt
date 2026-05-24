package com.example.moviebox_app.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Visibility
import androidx.compose.material.icons.filled.VisibilityOff
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.moviebox_app.ui.theme.Accent
import com.example.moviebox_app.ui.theme.Background
import com.example.moviebox_app.ui.theme.TextMuted
import com.example.moviebox_app.ui.theme.TextPrimary
import com.example.moviebox_app.ui.theme.TextSecondary
import com.example.moviebox_app.ui.viewmodel.AuthUiState
import com.example.moviebox_app.ui.viewmodel.AuthViewModel

@Composable
fun RegisterScreen(
    onRegisterSuccess: () -> Unit,
    onLoginClick: () -> Unit,
    vm: AuthViewModel = viewModel(),
) {
    val state by vm.state.collectAsState()

    var name                by remember { mutableStateOf("") }
    var email               by remember { mutableStateOf("") }
    var password            by remember { mutableStateOf("") }
    var passwordConfirm     by remember { mutableStateOf("") }
    var showPassword        by remember { mutableStateOf(false) }
    var showPasswordConfirm by remember { mutableStateOf(false) }

    LaunchedEffect(state) {
        if (state is AuthUiState.Success) onRegisterSuccess()
    }

    Box(modifier = Modifier.fillMaxSize().background(Background)) {
        Column(
            modifier = Modifier
                .fillMaxSize()
                .verticalScroll(rememberScrollState())
                .padding(horizontal = 28.dp, vertical = 48.dp),
            verticalArrangement = Arrangement.Center,
        ) {
            Text("Create account", color = TextMuted, fontSize = 14.sp)
            Spacer(Modifier.height(4.dp))
            Text("Register", color = TextPrimary, fontWeight = FontWeight.ExtraBold, fontSize = 28.sp)

            Spacer(Modifier.height(36.dp))

            AuthField(
                value = name,
                onValueChange = { name = it },
                placeholder = "Full name",
            )

            Spacer(Modifier.height(14.dp))

            AuthField(
                value = email,
                onValueChange = { email = it },
                placeholder = "Email address",
                keyboardType = KeyboardType.Email,
            )

            Spacer(Modifier.height(14.dp))

            AuthField(
                value = password,
                onValueChange = { password = it },
                placeholder = "Password",
                keyboardType = KeyboardType.Password,
                visualTransformation = if (showPassword) VisualTransformation.None else PasswordVisualTransformation(),
                trailingIcon = {
                    IconButton(onClick = { showPassword = !showPassword }, modifier = Modifier.size(20.dp)) {
                        Icon(
                            if (showPassword) Icons.Default.VisibilityOff else Icons.Default.Visibility,
                            null, tint = TextMuted, modifier = Modifier.size(18.dp),
                        )
                    }
                },
            )

            Spacer(Modifier.height(14.dp))

            AuthField(
                value = passwordConfirm,
                onValueChange = { passwordConfirm = it },
                placeholder = "Confirm password",
                keyboardType = KeyboardType.Password,
                visualTransformation = if (showPasswordConfirm) VisualTransformation.None else PasswordVisualTransformation(),
                trailingIcon = {
                    IconButton(onClick = { showPasswordConfirm = !showPasswordConfirm }, modifier = Modifier.size(20.dp)) {
                        Icon(
                            if (showPasswordConfirm) Icons.Default.VisibilityOff else Icons.Default.Visibility,
                            null, tint = TextMuted, modifier = Modifier.size(18.dp),
                        )
                    }
                },
            )

            if (state is AuthUiState.Error) {
                Spacer(Modifier.height(12.dp))
                Text(
                    (state as AuthUiState.Error).message,
                    color = Color(0xFFEF4444),
                    fontSize = 13.sp,
                )
            }

            Spacer(Modifier.height(28.dp))

            Button(
                onClick = { vm.register(name, email, password, passwordConfirm) },
                enabled = state !is AuthUiState.Loading,
                modifier = Modifier.fillMaxWidth().height(52.dp),
                shape = RoundedCornerShape(12.dp),
                colors = ButtonDefaults.buttonColors(containerColor = Accent, contentColor = Color.Black),
            ) {
                if (state is AuthUiState.Loading) {
                    CircularProgressIndicator(color = Color.Black, modifier = Modifier.size(20.dp), strokeWidth = 2.dp)
                } else {
                    Text("Create Account", fontWeight = FontWeight.Bold, fontSize = 15.sp)
                }
            }

            Spacer(Modifier.height(24.dp))

            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.Center,
            ) {
                Text("Already have an account? ", color = TextSecondary, fontSize = 14.sp)
                Text(
                    "Sign In",
                    color = Accent,
                    fontSize = 14.sp,
                    fontWeight = FontWeight.SemiBold,
                    modifier = Modifier.clickable(onClick = onLoginClick),
                )
            }
        }
    }
}
