// ========================================
// VARIABLES GLOBALES
// ========================================
let isProcessing = false

// ========================================
// INICIALIZACION
// ========================================
document.addEventListener("DOMContentLoaded", () => {
  initializeLoginApp()
})

function initializeLoginApp() {
  setupLoginEventListeners()
  setupLoginFormValidation()
  showNotification(document.body.dataset.welcome || "Bienvenido a PetSpa", "success")
}

// ========================================
// EVENT LISTENERS
// ========================================
function setupLoginEventListeners() {
  const loginForm = document.getElementById("loginFormElement")
  if (loginForm) {
    loginForm.addEventListener("submit", handleLogin)
  }

  const mfaForm = document.getElementById("mfaFormElement")
  if (mfaForm) {
    mfaForm.addEventListener("submit", handleMfaVerification)
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
  const remember = document.querySelector("input[name='remember']")?.checked || false

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

    const response = await fetch(e.target.getAttribute("action") || "/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": token,
        "Accept": "application/json",
      },
      body: JSON.stringify({
        correo: email,
        password: password,
        remember: remember,
      }),
    })

    const text = await response.text()
    let result

    try {
      result = JSON.parse(text)
    } catch (err) {
      console.error("Respuesta de login no valida:", err)
      showNotification("Error en el servidor", "error")
      return
    }

    if (result && result.success) {
      showNotification(`Bienvenido ${result.usuario.nombre}`, "success")

      setTimeout(() => {
        window.location.href = result.redirect || "/"
      }, 1500)
    } else if (result && result.mfa_required) {
      showMfaStep(result.message || "Ingresa el codigo MFA enviado a tu correo.")
    } else {
      const errorMsg = result.message || "Credenciales incorrectas"
      showNotification(errorMsg, "error")
    }
  } catch (error) {
    console.error("Error:", error)
    showNotification("Error de conexion. Verifica tu internet.", "error")
  } finally {
    setLoadingState(submitBtn, false)
    isProcessing = false
  }
}

async function handleMfaVerification(e) {
  e.preventDefault()

  if (isProcessing) return

  const code = document.getElementById("mfaCode").value.trim()
  const submitBtn = e.target.querySelector('button[type="submit"]')
  const buttonText = submitBtn.querySelector("span")
  const originalText = buttonText.textContent

  if (!/^\d{6}$/.test(code)) {
    showFieldError("mfaCode", "Ingresa un codigo de 6 digitos")
    return
  }

  submitBtn.disabled = true
  buttonText.textContent = "Verificando..."
  isProcessing = true

  try {
    const token = document
      .querySelector("meta[name='csrf-token']")
      .getAttribute("content")

    const response = await fetch(e.target.getAttribute("action") || "/login/mfa", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": token,
        "Accept": "application/json",
      },
      body: JSON.stringify({ code }),
    })

    const result = await response.json()

    if (result && result.success) {
      showNotification(result.message || "Verificacion completada", "success")

      setTimeout(() => {
        window.location.href = result.redirect || "/"
      }, 1000)
    } else {
      const errorMsg = result.message || "Codigo MFA incorrecto"
      showFieldError("mfaCode", errorMsg)
      showNotification(errorMsg, "error")
    }
  } catch (error) {
    console.error("Error MFA:", error)
    showNotification("Error de conexion al verificar MFA", "error")
  } finally {
    submitBtn.disabled = false
    buttonText.textContent = originalText
    isProcessing = false
  }
}

function showMfaStep(message) {
  const loginForm = document.getElementById("loginFormElement")
  const mfaForm = document.getElementById("mfaFormElement")
  const mfaText = document.getElementById("mfaMessageText")
  const mfaCode = document.getElementById("mfaCode")

  if (!mfaForm) return

  if (loginForm) {
    loginForm.hidden = true
  }

  document.querySelector(".form-footer")?.setAttribute("hidden", "")
  document.querySelector(".divider")?.setAttribute("hidden", "")
  document.querySelector(".social-login")?.setAttribute("hidden", "")

  if (mfaText) {
    mfaText.textContent = message
  }

  mfaForm.hidden = false

  if (mfaCode) {
    mfaCode.focus()
  }

  showNotification(message, "info")
}

// ========================================
// VALIDACIONES DE FORMULARIO
// ========================================
function validateLoginForm(email, password) {
  let isValid = true

  if (!email || !isValidEmail(email)) {
    showFieldError("loginEmail", "Por favor ingresa un email valido")
    isValid = false
  } else {
    showFieldSuccess("loginEmail")
  }

  if (!password) {
    showFieldError("loginPassword", "Ingresa tu contrasena")
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
        showFieldError("loginEmail", "Por favor ingresa un email valido")
      }
    })
  }
}

// ========================================
// UTILIDADES
// ========================================
function showFieldError(fieldId, message) {
  const field = document.getElementById(fieldId)
  if (!field) return

  const wrapper = field.closest(".input-wrapper")
  if (!wrapper) return

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
  if (!field) return

  const wrapper = field.closest(".input-wrapper")
  if (!wrapper) return

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
  const buttonText = button.querySelector("span")

  if (isLoading) {
    if (buttonText && !button.dataset.originalText) {
      button.dataset.originalText = buttonText.textContent
    }

    button.classList.add("loading")
    button.disabled = true
    if (buttonText) buttonText.textContent = "Iniciando sesion..."
  } else {
    button.classList.remove("loading")
    button.disabled = false
    if (buttonText) buttonText.textContent = button.dataset.originalText || "Iniciar Sesion"
    delete button.dataset.originalText
  }
}

function showNotification(message, type = "info") {
  const container = document.getElementById("toast-container")
  if (!container) return

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
