document.addEventListener("DOMContentLoaded", () => {
  // Mobile menu toggle
  const mobileMenuButton = document.getElementById("mobile-menu-button")
  const mobileMenu = document.getElementById("mobile-menu")

  if (mobileMenuButton && mobileMenu) {
    mobileMenuButton.addEventListener("click", () => {
      mobileMenu.classList.toggle("hidden")
    })
  }

  // Testimonial slider
  const testimonialSlider = document.querySelector(".testimonial-slider")
  if (testimonialSlider) {
    // Simple slider functionality
    let currentSlide = 0
    const slides = testimonialSlider.querySelectorAll("div.bg-gray-50")

    if (slides.length > 1) {
      // Hide all slides except the first one
      for (let i = 1; i < slides.length; i++) {
        slides[i].style.display = "none"
      }

      // Create navigation dots
      const dotsContainer = document.createElement("div")
      dotsContainer.className = "flex justify-center mt-6 space-x-2"

      for (let i = 0; i < slides.length; i++) {
        const dot = document.createElement("button")
        dot.className = i === 0 ? "w-3 h-3 rounded-full bg-blue-500" : "w-3 h-3 rounded-full bg-gray-300"
        dot.setAttribute("data-slide", i)
        dot.addEventListener("click", function () {
          goToSlide(Number.parseInt(this.getAttribute("data-slide")))
        })
        dotsContainer.appendChild(dot)
      }

      testimonialSlider.parentNode.appendChild(dotsContainer)

      // Auto slide every 5 seconds
      setInterval(nextSlide, 5000)

      function nextSlide() {
        goToSlide((currentSlide + 1) % slides.length)
      }

      function goToSlide(n) {
        slides[currentSlide].style.display = "none"
        dotsContainer.children[currentSlide].className = "w-3 h-3 rounded-full bg-gray-300"

        currentSlide = n

        slides[currentSlide].style.display = "block"
        dotsContainer.children[currentSlide].className = "w-3 h-3 rounded-full bg-blue-500"
      }
    }
  }

  // Order modal
  const orderButtons = document.querySelectorAll(".order-button")
  const orderModal = document.getElementById("order-modal")
  const closeModal = document.getElementById("close-modal")
  const modalTitle = document.getElementById("modal-title")
  const packageIdInput = document.getElementById("package_id")

  if (orderButtons.length > 0 && orderModal && closeModal) {
    orderButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const packageId = this.getAttribute("data-package-id")
        const packageName = this.getAttribute("data-package-name")

        packageIdInput.value = packageId
        modalTitle.textContent = `Commander ${packageName}`

        orderModal.classList.remove("hidden")
      })
    })

    closeModal.addEventListener("click", () => {
      orderModal.classList.add("hidden")
    })

    // Close modal when clicking outside
    window.addEventListener("click", (event) => {
      if (event.target === orderModal) {
        orderModal.classList.add("hidden")
      }
    })
  }

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()

      const targetId = this.getAttribute("href")
      if (targetId === "#") return

      const targetElement = document.querySelector(targetId)
      if (targetElement) {
        window.scrollTo({
          top: targetElement.offsetTop - 80, // Adjust for header height
          behavior: "smooth",
        })

        // Close mobile menu if open
        if (mobileMenu && !mobileMenu.classList.contains("hidden")) {
          mobileMenu.classList.add("hidden")
        }
      }
    })
  })

  // Notification system
  function showNotification(message, type = "success") {
    // Remove any existing notification
    const existingNotification = document.getElementById("notification")
    if (existingNotification) {
      existingNotification.remove()
    }

    // Create notification element
    const notification = document.createElement("div")
    notification.id = "notification"

    // Set styles based on type
    let bgColor, icon
    if (type === "success") {
      bgColor = "bg-kmergreen"
      icon = '<i class="fas fa-check-circle mr-2"></i>'
    } else if (type === "error") {
      bgColor = "bg-red-500"
      icon = '<i class="fas fa-exclamation-circle mr-2"></i>'
    } else if (type === "info") {
      bgColor = "bg-blue-500"
      icon = '<i class="fas fa-info-circle mr-2"></i>'
    }

    notification.className = `fixed top-20 right-4 z-50 ${bgColor} text-white px-4 py-3 rounded-lg shadow-lg transform transition-all duration-500 flex items-center`
    notification.innerHTML = `
      ${icon}
      <span>${message}</span>
      <button class="ml-4 text-white hover:text-gray-200 focus:outline-none">
        <i class="fas fa-times"></i>
      </button>
    `

    // Add to DOM
    document.body.appendChild(notification)

    // Add animation
    setTimeout(() => {
      notification.style.transform = "translateX(0)"
    }, 10)

    // Add close button functionality
    const closeButton = notification.querySelector("button")
    closeButton.addEventListener("click", () => {
      notification.style.transform = "translateX(400px)"
      setTimeout(() => {
        notification.remove()
      }, 500)
    })

    // Auto close after 5 seconds
    setTimeout(() => {
      if (notification.parentNode) {
        notification.style.transform = "translateX(400px)"
        setTimeout(() => {
          if (notification.parentNode) {
            notification.remove()
          }
        }, 500)
      }
    }, 5000)
  }

  // Make notification function globally available
  window.showNotification = showNotification
})

// Fonction pour ajouter au panier
function addToCart(packageId, packageType, quantity = 1, customDomain = null) {
  // Afficher un indicateur de chargement
  const loadingIndicator = document.createElement("div")
  loadingIndicator.id = "loading-indicator"
  loadingIndicator.className =
    "fixed top-0 left-0 w-full h-full flex items-center justify-center bg-black bg-opacity-50 z-50"
  loadingIndicator.innerHTML =
    '<div class="bg-white p-5 rounded-lg shadow-lg"><i class="fas fa-spinner fa-spin text-kmergreen text-3xl"></i><p class="mt-2 text-gray-700">Traitement en cours...</p></div>'
  document.body.appendChild(loadingIndicator)

  // Créer un objet FormData pour envoyer les données
  const formData = new FormData()
  formData.append("package_id", packageId)
  formData.append("package_type", packageType)
  formData.append("quantity", quantity)

  if (customDomain) {
    formData.append("custom_domain", customDomain)
  }

  // Envoyer la requête AJAX
  fetch("/backend/cart/add_to_cart.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      // Supprimer l'indicateur de chargement
      document.getElementById("loading-indicator").remove()

      if (data.success) {
        // Afficher un message de succès stylisé
        window.showNotification(data.message, "success")

        // Mettre à jour le compteur du panier
        const cartCountBadges = document.querySelectorAll(".cart-count-badge")
        if (cartCountBadges.length > 0) {
          const currentCount = Number.parseInt(cartCountBadges[0].textContent || "0")
          const newCount = currentCount + 1

          cartCountBadges.forEach((badge) => {
            badge.textContent = newCount
            badge.classList.remove("hidden")
          })
        }
      } else {
        // Afficher un message d'erreur stylisé
        window.showNotification("Erreur: " + data.message, "error")

        // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
        if (data.message.includes("connecté")) {
          setTimeout(() => {
            window.location.href = "/login.php"
          }, 2000)
        }
      }
    })
    .catch((error) => {
      // Supprimer l'indicateur de chargement
      if (document.getElementById("loading-indicator")) {
        document.getElementById("loading-indicator").remove()
      }

      console.error("Erreur:", error)
      window.showNotification("Une erreur est survenue lors de l'ajout au panier.", "error")
    })
}

// Ajouter des écouteurs d'événements pour les boutons d'ajout au panier
document.addEventListener("DOMContentLoaded", () => {
  const addToCartButtons = document.querySelectorAll(".add-to-cart-btn")

  if (addToCartButtons.length > 0) {
    addToCartButtons.forEach((button) => {
      button.addEventListener("click", function (e) {
        e.preventDefault()

        const packageId = this.getAttribute("data-package-id")
        const packageType = this.getAttribute("data-package-type")
        const quantity = 1

        // Pour les domaines, demander le nom de domaine
        if (packageType === "domain") {
          const customDomain = prompt("Veuillez entrer le nom de domaine souhaité:")
          if (customDomain) {
            addToCart(packageId, packageType, quantity, customDomain)
          }
        } else {
          addToCart(packageId, packageType, quantity)
        }
      })
    })
  }
})
