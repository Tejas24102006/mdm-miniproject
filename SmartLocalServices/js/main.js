// Build the API URL from the current host so the app works on localhost,
// custom local domains, and non-default Apache ports.
const API_URL = (() => {
  if (window.location.protocol === "file:") {
    return "http://localhost/SmartLocalServices/api";
  }

  const pathParts = window.location.pathname.split("/").filter(Boolean);
  const projectIndex = pathParts.findIndex(
    (part) => part.toLowerCase() === "smartlocalservices",
  );
  const projectPath =
    projectIndex >= 0
      ? `/${pathParts.slice(0, projectIndex + 1).join("/")}`
      : "";

  return `${window.location.origin}${projectPath}/api`;
})();

// Utility: Show Alert Message
function showAlert(elementId, message, type) {
  const alertEl = document.getElementById(elementId);
  if (!alertEl) return;
  alertEl.textContent = message;
  alertEl.className = `alert ${type}`;
  // Auto-hide after 5 seconds
  setTimeout(() => {
    alertEl.style.display = "none";
    alertEl.className = "alert";
  }, 5000);
}

// Global Auth Check & Navbar Update
function updateNavbar() {
  const user = JSON.parse(localStorage.getItem("sls_user"));
  const authLinksLinks = document.getElementById("auth-links");
  const userNav = document.getElementById("user-nav");

  // Adjust path depth dynamically based on where we are
  const isPagesDir = window.location.pathname.includes("/pages/");
  const basePath = isPagesDir ? "../" : "";
  const pagesPath = isPagesDir ? "" : "pages/";

  if (user) {
    if (authLinksLinks) authLinksLinks.style.display = "none";

    let dashboardLink = "customer-dashboard.html";
    if (user.role === "provider") dashboardLink = "provider-dashboard.html";
    if (user.role === "admin") dashboardLink = "admin-dashboard.html";

    if (userNav) {
      userNav.style.display = "flex";
      userNav.innerHTML = `
                <li><a href="${pagesPath}services.html">Find Services</a></li>
                <li><a href="${pagesPath}${dashboardLink}">Dashboard</a></li>
                <li><a href="#" onclick="logout()">Logout</a></li>
            `;
    }
  } else {
    if (authLinksLinks) {
      authLinksLinks.style.display = "flex";
      authLinksLinks.innerHTML = `
                <li><a href="${pagesPath}services.html">Find Services</a></li>
                <li><a href="${pagesPath}login.html">Login</a></li>
                <li><a href="${pagesPath}register.html" class="btn">Sign Up</a></li>
            `;
    }
    if (userNav) userNav.style.display = "none";
  }
}

function logout() {
  localStorage.removeItem("sls_user");
  const basePath = window.location.pathname.includes("/pages/")
    ? "../index.html"
    : "index.html";
  window.location.href = basePath;
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
  updateNavbar();
});
