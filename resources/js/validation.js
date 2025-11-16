/* ===================================
   Validation Functions
   =================================== */

/**
 * Validate email format
 * @param {string} email - Email to validate
 * @returns {boolean} - True if valid
 */
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate phone number (Vietnamese format)
 * @param {string} phone - Phone number to validate
 * @returns {boolean} - True if valid
 */
function validatePhone(phone) {
    // Vietnamese phone: 10 digits, starts with 0
    const phoneRegex = /^0\d{9}$/;
    return phoneRegex.test(phone.replace(/\s+/g, ''));
}

/**
 * Check password strength
 * @param {string} password - Password to check
 * @returns {object} - Strength score and feedback
 */
function checkPasswordStrength(password) {
    let score = 0;
    let feedback = [];
    
    if (!password) {
        return { score: 0, level: 'none', feedback: ['Nhập mật khẩu'] };
    }
    
    // Length check
    if (password.length >= 8) {
        score += 1;
    } else {
        feedback.push('Ít nhất 8 ký tự');
    }
    
    // Lowercase check
    if (/[a-z]/.test(password)) {
        score += 1;
    } else {
        feedback.push('Có chữ thường');
    }
    
    // Uppercase check
    if (/[A-Z]/.test(password)) {
        score += 1;
    } else {
        feedback.push('Có chữ hoa');
    }
    
    // Number check
    if (/\d/.test(password)) {
        score += 1;
    } else {
        feedback.push('Có số');
    }
    
    // Special character check
    if (/[^a-zA-Z0-9]/.test(password)) {
        score += 1;
    }
    
    // Determine level
    let level = 'weak';
    if (score >= 4) {
        level = 'strong';
    } else if (score >= 3) {
        level = 'medium';
    }
    
    return {
        score: score,
        level: level,
        feedback: feedback.length > 0 ? feedback : ['Mật khẩu mạnh']
    };
}

/**
 * Update password strength UI
 * @param {object} strength - Strength data from checkPasswordStrength
 * @param {HTMLElement} fillElement - Progress bar fill element
 * @param {HTMLElement} textElement - Text display element
 */
function updatePasswordStrength(strength, fillElement, textElement) {
    // Remove all classes
    fillElement.className = 'strength-fill';
    textElement.className = 'strength-text';
    
    // Add level class
    if (strength.level !== 'none') {
        fillElement.classList.add(strength.level);
        textElement.classList.add(strength.level);
    }
    
    // Update text
    const levelText = {
        'none': 'Nhập mật khẩu',
        'weak': 'Yếu',
        'medium': 'Trung bình',
        'strong': 'Mạnh'
    };
    
    textElement.textContent = levelText[strength.level] || strength.feedback.join(', ');
}

/**
 * Show error message for a field
 * @param {string} errorId - ID of error message element
 * @param {string} message - Error message to display
 */
function showError(errorId, message) {
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.textContent = message;
        
        // Add error class to input
        const input = errorElement.previousElementSibling;
        if (input && input.tagName === 'INPUT') {
            input.classList.add('error');
        } else if (input && input.classList.contains('input-with-icon')) {
            input.querySelector('input').classList.add('error');
        }
    }
}

/**
 * Clear error message for a field
 * @param {string} errorId - ID of error message element
 */
function clearError(errorId) {
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.textContent = '';
        
        // Remove error class from input
        const input = errorElement.previousElementSibling;
        if (input && input.tagName === 'INPUT') {
            input.classList.remove('error');
        } else if (input && input.classList.contains('input-with-icon')) {
            input.querySelector('input').classList.remove('error');
        }
    }
}

/**
 * Clear all form errors
 * @param {HTMLFormElement} form - Form element to clear
 */
function clearFormErrors(form) {
    const errorMessages = form.querySelectorAll('.error-message');
    errorMessages.forEach(error => {
        error.textContent = '';
    });
    
    const errorInputs = form.querySelectorAll('.error');
    errorInputs.forEach(input => {
        input.classList.remove('error');
    });
}

/**
 * Validate credit card number (Luhn algorithm)
 * @param {string} cardNumber - Card number to validate
 * @returns {boolean} - True if valid
 */
function validateCreditCard(cardNumber) {
    // Remove spaces and dashes
    const cleaned = cardNumber.replace(/[\s-]/g, '');
    
    // Check if only digits
    if (!/^\d+$/.test(cleaned)) {
        return false;
    }
    
    // Check length (13-19 digits)
    if (cleaned.length < 13 || cleaned.length > 19) {
        return false;
    }
    
    // Luhn algorithm
    let sum = 0;
    let isEven = false;
    
    for (let i = cleaned.length - 1; i >= 0; i--) {
        let digit = parseInt(cleaned[i], 10);
        
        if (isEven) {
            digit *= 2;
            if (digit > 9) {
                digit -= 9;
            }
        }
        
        sum += digit;
        isEven = !isEven;
    }
    
    return sum % 10 === 0;
}

/**
 * Get card type from card number
 * @param {string} cardNumber - Card number
 * @returns {string} - Card type (visa, mastercard, etc.)
 */
function getCardType(cardNumber) {
    const cleaned = cardNumber.replace(/[\s-]/g, '');
    
    if (/^4/.test(cleaned)) {
        return 'visa';
    } else if (/^5[1-5]/.test(cleaned)) {
        return 'mastercard';
    } else if (/^3[47]/.test(cleaned)) {
        return 'amex';
    } else if (/^6(?:011|5)/.test(cleaned)) {
        return 'discover';
    } else if (/^35/.test(cleaned)) {
        return 'jcb';
    }
    
    return 'unknown';
}

/**
 * Format card number with spaces
 * @param {string} cardNumber - Card number
 * @returns {string} - Formatted card number
 */
function formatCardNumber(cardNumber) {
    const cleaned = cardNumber.replace(/[\s-]/g, '');
    const groups = cleaned.match(/.{1,4}/g);
    return groups ? groups.join(' ') : cleaned;
}

/**
 * Validate expiry date (MM/YY format)
 * @param {string} expiry - Expiry date
 * @returns {boolean} - True if valid and not expired
 */
function validateExpiryDate(expiry) {
    const cleaned = expiry.replace(/\s/g, '');
    const parts = cleaned.split('/');
    
    if (parts.length !== 2) {
        return false;
    }
    
    const month = parseInt(parts[0], 10);
    const year = parseInt('20' + parts[1], 10);
    
    if (month < 1 || month > 12) {
        return false;
    }
    
    const now = new Date();
    const currentYear = now.getFullYear();
    const currentMonth = now.getMonth() + 1;
    
    if (year < currentYear || (year === currentYear && month < currentMonth)) {
        return false;
    }
    
    return true;
}

/**
 * Validate CVV
 * @param {string} cvv - CVV code
 * @param {string} cardType - Card type (amex requires 4 digits)
 * @returns {boolean} - True if valid
 */
function validateCVV(cvv, cardType = 'visa') {
    const cleaned = cvv.replace(/\s/g, '');
    
    if (cardType === 'amex') {
        return /^\d{4}$/.test(cleaned);
    }
    
    return /^\d{3}$/.test(cleaned);
}

/**
 * Sanitize input (remove HTML tags)
 * @param {string} input - Input string
 * @returns {string} - Sanitized string
 */
function sanitizeInput(input) {
    const div = document.createElement('div');
    div.textContent = input;
    return div.innerHTML;
}

/**
 * Validate URL
 * @param {string} url - URL to validate
 * @returns {boolean} - True if valid
 */
function validateURL(url) {
    try {
        new URL(url);
        return true;
    } catch (e) {
        return false;
    }
}

/**
 * Validate date (YYYY-MM-DD format)
 * @param {string} dateString - Date string
 * @returns {boolean} - True if valid
 */
function validateDate(dateString) {
    const regex = /^\d{4}-\d{2}-\d{2}$/;
    if (!regex.test(dateString)) {
        return false;
    }
    
    const date = new Date(dateString);
    return date instanceof Date && !isNaN(date);
}

/**
 * Check if date is in the future
 * @param {string} dateString - Date string (YYYY-MM-DD)
 * @returns {boolean} - True if future date
 */
function isFutureDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    now.setHours(0, 0, 0, 0);
    return date >= now;
}

/**
 * Debounce function for real-time validation
 * @param {function} func - Function to debounce
 * @param {number} wait - Wait time in milliseconds
 * @returns {function} - Debounced function
 */
function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Form validation helper
 * @param {HTMLFormElement} form - Form to validate
 * @param {object} rules - Validation rules
 * @returns {boolean} - True if all fields valid
 */
function validateForm(form, rules) {
    let isValid = true;
    
    for (const [fieldName, fieldRules] of Object.entries(rules)) {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (!field) continue;
        
        const value = field.value.trim();
        const errorId = fieldName + 'Error';
        
        // Required check
        if (fieldRules.required && !value) {
            showError(errorId, fieldRules.required);
            isValid = false;
            continue;
        }
        
        // Skip other validations if empty and not required
        if (!value) continue;
        
        // Min length check
        if (fieldRules.minLength && value.length < fieldRules.minLength) {
            showError(errorId, `Phải có ít nhất ${fieldRules.minLength} ký tự`);
            isValid = false;
            continue;
        }
        
        // Max length check
        if (fieldRules.maxLength && value.length > fieldRules.maxLength) {
            showError(errorId, `Không được vượt quá ${fieldRules.maxLength} ký tự`);
            isValid = false;
            continue;
        }
        
        // Pattern check
        if (fieldRules.pattern && !fieldRules.pattern.test(value)) {
            showError(errorId, fieldRules.patternMessage || 'Định dạng không hợp lệ');
            isValid = false;
            continue;
        }
        
        // Custom validation
        if (fieldRules.custom && !fieldRules.custom(value)) {
            showError(errorId, fieldRules.customMessage || 'Giá trị không hợp lệ');
            isValid = false;
            continue;
        }
        
        // Clear error if valid
        clearError(errorId);
    }
    
    return isValid;
}

// Export functions (if using modules)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        validateEmail,
        validatePhone,
        checkPasswordStrength,
        updatePasswordStrength,
        showError,
        clearError,
        clearFormErrors,
        validateCreditCard,
        getCardType,
        formatCardNumber,
        validateExpiryDate,
        validateCVV,
        sanitizeInput,
        validateURL,
        validateDate,
        isFutureDate,
        debounce,
        validateForm
    };
}
