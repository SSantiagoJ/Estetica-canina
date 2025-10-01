// ========================================
// VARIABLES GLOBALES
// ========================================
let isProcessing = false

// ========================================
// INICIALIZACIÓN
// ========================================
document.addEventListener("DOMContentLoaded", () => {
  setupRegisterEventListeners()
  setupRegisterFormValidation()
  showNotification("¡Bienvenido a PetSpa! Crea tu cuenta", "info")
})

// ========================================
// EVENT LISTENERS
// ========================================
function setupRegisterEventListeners() {
  const registerForm = document.getElementById("registerFormElement")
  if (registerForm) {
    registerForm.addEventListener("submit", handleRegister)
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
    tipo_persona: "Cliente" // por defecto
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
        "Accept": "application/json"
      },
      body: JSON.stringify(formData)
    })

    const text = await response.text()
    console.log("Respuesta cruda registro:", text)

    let result
    try {
      result = JSON.parse(text)
    } catch (err) {
      console.error("Registro: respuesta no JSON:", err)
      showNotification("Error en el servidor - respuesta inválida", "error")
      return
    }

    if (result && result.success) {
      showNotification("¡Registro exitoso! Redirigiendo al login...", "success")
      setTimeout(() => (window.location.href = "/login"), 2000)
    } else {
      const errorMsg = result.message || "Error en el registro"
      showNotification(errorMsg, "error")
    }
  } catch (error) {
    console.error("Error en fetch/registro:", error)
    showNotification("Error de conexión al servidor", "error")
  } finally {
    setLoadingState(submitBtn, false)
    isProcessing = false
  }
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
    showFieldError("dni", "Por favor ingresa un documento válido")
    isValid = false
  } else showFieldSuccess("dni")

  if (!data.correo || !isValidEmail(data.correo)) {
    showFieldError("registerEmail", "Por favor ingresa un correo válido")
    isValid = false
  } else showFieldSuccess("registerEmail")

  if (!data.password || data.password.length < 6) {
    showFieldError("registerPassword", "La contraseña debe tener al menos 6 caracteres")
    isValid = false
  } else showFieldSuccess("registerPassword")

  if (data.password !== data.password_confirmation) {
    showFieldError("confirmPassword", "Las contraseñas no coinciden")
    isValid = false
  } else showFieldSuccess("confirmPassword")

  return isValid
}

// ========================================
// MANEJO DE ERRORES EN CAMPOS
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
    info: "fas fa-info-circle"
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
