// ========================================
// VARIABLES GLOBALES
// ========================================
let isProcessing = false

// ========================================
// INICIALIZACION
// ========================================
document.addEventListener("DOMContentLoaded", () => {
  setupRegisterEventListeners()
  setupRegisterFormValidation()
  showNotification("Bienvenido a PetSpa. Crea tu cuenta", "info")
})

// ========================================
// EVENT LISTENERS
// ========================================
function setupRegisterEventListeners() {
  const registerForm = document.getElementById("registerFormElement")
  if (registerForm) {
    registerForm.addEventListener("submit", handleRegister)
  }

  const mfaForm = document.getElementById("registerMfaFormElement")
  if (mfaForm) {
    mfaForm.addEventListener("submit", handleRegisterMfaVerification)
  }
}

// ========================================
// MANEJO DE REGISTRO
// ========================================
async function handleRegister(e) {
  e.preventDefault()
  if (isProcessing) return

  const formData = {
    nombres: document.getElementById("firstName").value.trim(),
    apellidos: document.getElementById("lastName").value.trim(),
    tipo_doc: document.getElementById("documentType").value,
    nro_documento: document.getElementById("dni").value.trim(),
    correo: document.getElementById("registerEmail").value.trim(),
    password: document.getElementById("registerPassword").value,
    password_confirmation: document.getElementById("confirmPassword").value,
    tipo_persona: "Cliente",
  }

  if (!validateRegisterForm(formData)) return

  const submitBtn = e.target.querySelector('button[type="submit"]')
  setLoadingState(submitBtn, true)
  isProcessing = true

  try {
    const token = document.querySelector("meta[name='csrf-token']").getAttribute("content")

    const response = await fetch("/register", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": token,
        "Accept": "application/json",
      },
      body: JSON.stringify(formData),
    })

    const result = await response.json()

    if (result && result.success) {
      showNotification("Registro exitoso. Redirigiendo...", "success")
      setTimeout(() => (window.location.href = result.redirect || "/"), 1500)
    } else if (result && result.mfa_required) {
      showRegisterMfaStep(result.message || "Ingresa el codigo MFA enviado a tu correo.")
    } else {
      showNotification(extractErrorMessage(result, "Error en el registro"), "error")
    }
  } catch (error) {
    console.error("Error en registro:", error)
    showNotification("Error de conexion al servidor", "error")
  } finally {
    setLoadingState(submitBtn, false)
    isProcessing = false
  }
}

async function handleRegisterMfaVerification(e) {
  e.preventDefault()

  if (isProcessing) return

  const code = document.getElementById("registerMfaCode").value.trim()
  const submitBtn = e.target.querySelector('button[type="submit"]')
  const buttonText = submitBtn.querySelector("span")
  const originalText = buttonText.textContent

  if (!/^\d{6}$/.test(code)) {
    showFieldError("registerMfaCode", "Ingresa un codigo de 6 digitos")
    return
  }

  submitBtn.disabled = true
  buttonText.textContent = "Verificando..."
  isProcessing = true

  try {
    const token = document.querySelector("meta[name='csrf-token']").getAttribute("content")

    const response = await fetch("/login/mfa", {
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
      showNotification(result.message || "Cuenta verificada", "success")
      setTimeout(() => (window.location.href = result.redirect || "/"), 1000)
    } else {
      const errorMsg = result.message || "Codigo MFA incorrecto"
      showFieldError("registerMfaCode", errorMsg)
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

function showRegisterMfaStep(message) {
  const registerForm = document.getElementById("registerFormElement")
  const mfaForm = document.getElementById("registerMfaFormElement")
  const mfaText = document.getElementById("registerMfaMessageText")
  const mfaCode = document.getElementById("registerMfaCode")

  if (!mfaForm) return

  if (registerForm) {
    registerForm.hidden = true
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
// VALIDACIONES
// ========================================
function validateRegisterForm(data) {
  let isValid = true

  if (!data.nombres || data.nombres.length < 2) {
    showFieldError("firstName", "El nombre debe tener al menos 2 caracteres")
    isValid = false
  } else showFieldSuccess("firstName")

  if (!data.apellidos || data.apellidos.length < 2) {
    showFieldError("lastName", "El apellido debe tener al menos 2 caracteres")
    isValid = false
  } else showFieldSuccess("lastName")

  if (!data.tipo_doc) {
    showFieldError("documentType", "Selecciona un tipo de documento")
    isValid = false
  } else showFieldSuccess("documentType")

  if (!data.nro_documento) {
    showFieldError("dni", "Por favor ingresa un documento valido")
    isValid = false
  } else showFieldSuccess("dni")

  if (!data.correo || !isValidEmail(data.correo)) {
    showFieldError("registerEmail", "Por favor ingresa un correo valido")
    isValid = false
  } else showFieldSuccess("registerEmail")

  if (!isStrongPassword(data.password)) {
    showFieldError(
      "registerPassword",
      "Debe tener mas de 8 caracteres, mayuscula, minuscula, numero y simbolo"
    )
    isValid = false
  } else showFieldSuccess("registerPassword")

  if (data.password !== data.password_confirmation) {
    showFieldError("confirmPassword", "Las contrasenas no coinciden")
    isValid = false
  } else showFieldSuccess("confirmPassword")

  return isValid
}

// ========================================
// MANEJO DE ERRORES EN CAMPOS
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

// ========================================
// UTILIDADES
// ========================================
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
    button.querySelector("span").textContent = "Creando cuenta..."
  } else {
    button.classList.remove("loading")
    button.disabled = false
    button.querySelector("span").textContent = "Crear Cuenta"
  }
}

// ========================================
// TOAST NOTIFICATIONS
// ========================================
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
    setTimeout(() => toast.remove(), 300)
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

// ========================================
// HELPERS
// ========================================
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

function isStrongPassword(password) {
  return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{9,}$/.test(password)
}

function extractErrorMessage(result, fallback) {
  if (result?.message) return result.message

  const firstError = result?.errors ? Object.values(result.errors)[0] : null
  if (Array.isArray(firstError) && firstError.length > 0) {
    return firstError[0]
  }

  return fallback
}

function setupRegisterFormValidation() {
  const form = document.getElementById("registerFormElement")
  if (form) {
    form.addEventListener("submit", (e) => {
      const requiredFields = form.querySelectorAll("input[required], select[required]")
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
