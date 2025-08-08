/* public/css/custom-admin.css */

/* Inter Font - as specified in your design document */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

body {
    font-family: 'Inter', sans-serif;
    background-color: #f8f9fa; /* A light, professional background */
}

/* Gradient Backgrounds from your Design System */
.bg-gradient-primary {
    background-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.bg-gradient-success {
    background-image: linear-gradient(135deg, #10b981 0%, #059669 100%);
}
.bg-gradient-warning {
    background-image: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}
.bg-gradient-danger {
    background-image: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

/* Professional Card Styling */
.professional-card {
    border-radius: 12px; /* As per your design spec */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.professional-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
}

.stats-icon-wrapper {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Quick Action Card with Gradient Border */
.action-card-gradient-border {
    border-radius: 20px !important; /* As per your design spec */
    border: 2px solid transparent;
    background-clip: padding-box;
    position: relative;
    transition: all 0.3s ease-in-out;
}

.action-card-gradient-border::before {
    content: '';
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    z-index: -1;
    margin: -2px; /* Border width */
    border-radius: inherit; /* Follow card's border-radius */
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

.action-card-gradient-border:hover::before {
    opacity: 1;
}

.action-card-gradient-border:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
}

.action-card-gradient-border .fa-3x {
    transition: transform 0.3s ease;
}

.action-card-gradient-border:hover .fa-3x {
    transform: scale(1.1);
}
