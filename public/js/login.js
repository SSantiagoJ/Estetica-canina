// ========================================
// VARIABLES GLOBALES
// ========================================
let isProcessing = false

// ========================================
// INICIALIZACIÓN
// ========================================
document.addEventListener("DOMContentLoaded", () => {
  initializeLoginApp()
})

function initializeLoginApp() {
  setupLoginEventListeners()
  setupLoginFormValidation()
  showNotification("¡Bienvenido a PetSpa!", "success")
}

// ========================================
// EVENT LISTENERS
// ========================================
function setupLoginEventListeners() {
  const loginForm = document.getElementById("loginFormElement")
  if (loginForm) {
    loginForm.addEventListener("submit", handleLogin)
  }
  setupLoginRealTimeValidation()
}

// ========================================
// MANEJO DE LOGIN
// ========================================
async function handleLogin(e) {
  e.preventDefault()

  if (isProcessing) return

  const email = document.querySelector("input[name='correo']").value.trim()
  const password = document.querySelector("input[name='password']").value

  if (!validateLoginForm(email, password)) {
    return
  }

  const submitBtn = e.target.querySelector('button[type="submit"]')
  setLoadingState(submitBtn, true)
  isProcessing = true

  try {
    const token = document
      .querySelector("meta[name='csrf-token']")
      .getAttribute("content")

    const response = await fetch("/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": token,
        "Accept": "application/json"
      },
      body: JSON.stringify({
        correo: email,
        password: password,
      }),
    })

    const text = await response.text()
    console.log("Respuesta cruda del servidor:", text)

    let result
    try {
      result = JSON.parse(text)
    } catch (err) {
      console.error("No es JSON válido:", err)
      showNotification("Error en el servidor", "error")
      return
    }

    if (result && result.success) {
      showNotification(`¡Bienvenido ${result.usuario.nombre}!`, "success")

      setTimeout(() => {
        switch (result.usuario.rol) {
          case "Admin":
            window.location.href = "/admin_dashboard"
            break
          case "Empleado":
            window.location.href = "/empleado/bandeja-reservas"
             break
          default:
            window.location.href = "/dashboard"
        }
      }, 1500)
    } else {
      const errorMsg = result.message || "Credenciales incorrectas"
      showNotification(errorMsg, "error")
    }
  } catch (error) {
    console.error("Error:", error)
    showNotification("Error de conexión. Verifica tu internet.", "error")
  } finally {
    setLoadingState(submitBtn, false)
    isProcessing = false
  }
}

// ========================================
// VALIDACIONES DE FORMULARIO
// ========================================
function validateLoginForm(email, password) {
  let isValid = true

  if (!email || !isValidEmail(email)) {
    showFieldError("loginEmail", "Por favor ingresa un email válido")
    isValid = false
  } else {
    showFieldSuccess("loginEmail")
  }

  if (!password || password.length < 6) {
    showFieldError("loginPassword", "La contraseña debe tener al menos 6 caracteres")
    isValid = false
  } else {
    showFieldSuccess("loginPassword")
  }

  return isValid
}

function setupLoginRealTimeValidation() {
  const loginEmail = document.getElementById("loginEmail")
  if (loginEmail) {
    loginEmail.addEventListener("input", function () {
      const email = this.value.trim()
      if (email && isValidEmail(email)) {
        showFieldSuccess("loginEmail")
      } else if (email) {
        showFieldError("loginEmail", "Por favor ingresa un email válido")
      }
    })
  }
}

// ========================================
// UTILIDADES
// ========================================
function showFieldError(fieldId, message) {
  const field = document.getElementById(fieldId)
  const wrapper = field.closest(".input-wrapper")

  wrapper.classList.remove("success")
  wrapper.classList.add("error")

  const existingMessage = wrapper.parentNode.querySelector(".error-message")
  if (existingMessage) existingMessage.remove()

  const errorDiv = document.createElement("div")
  errorDiv.className = "error-message"
  errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`
  wrapper.parentNode.appendChild(errorDiv)
}

function showFieldSuccess(fieldId) {
  const field = document.getElementById(fieldId)
  const wrapper = field.closest(".input-wrapper")

  wrapper.classList.remove("error")
  wrapper.classList.add("success")

  const existingMessage = wrapper.parentNode.querySelector(".error-message")
  if (existingMessage) existingMessage.remove()
}

function togglePassword(fieldId) {
  const field = document.getElementById(fieldId)
  const button = field.nextElementSibling
  const icon = button.querySelector("i")

  if (field.type === "password") {
    field.type = "text"
    icon.classList.remove("fa-eye")
    icon.classList.add("fa-eye-slash")
  } else {
    field.type = "password"
    icon.classList.remove("fa-eye-slash")
    icon.classList.add("fa-eye")
  }
}

function setLoadingState(button, isLoading) {
  if (isLoading) {
    button.classList.add("loading")
    button.disabled = true
    button.querySelector("span").textContent = "Iniciando sesión..."
  } else {
    button.classList.remove("loading")
    button.disabled = false
    button.querySelector("span").textContent = "Iniciar Sesión"
  }
}

function showNotification(message, type = "info") {
  const container = document.getElementById("toast-container")
  const toast = document.createElement("div")
  toast.className = `toast ${type}`

  const icon = getNotificationIcon(type)
  toast.innerHTML = `<i class="${icon}"></i><span>${message}</span>`

  container.appendChild(toast)

  setTimeout(() => {
    toast.style.animation = "slideOutRight 0.3s ease"
    setTimeout(() => {
      if (toast.parentNode) toast.parentNode.removeChild(toast)
    }, 300)
  }, 5000)
}

function getNotificationIcon(type) {
  const icons = {
    success: "fas fa-check-circle",
    error: "fas fa-exclamation-circle",
    warning: "fas fa-exclamation-triangle",
    info: "fas fa-info-circle",
  }
  return icons[type] || icons.info
}

function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

function setupLoginFormValidation() {
  const form = document.getElementById("loginFormElement")
  if (form) {
    form.addEventListener("submit", (e) => {
      const requiredFields = form.querySelectorAll("input[required]")
      let hasEmptyFields = false

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          hasEmptyFields = true
          showFieldError(field.id, "Este campo es obligatorio")
        }
      })

      if (hasEmptyFields) {
        e.preventDefault()
        showNotification("Por favor completa todos los campos obligatorios", "warning")
      }
    })
  }
}
