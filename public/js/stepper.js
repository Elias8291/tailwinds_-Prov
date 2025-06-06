document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.getElementById('progress-bar');
    const progressLine = document.getElementById('progress-line');
    const progressPercentage = document.getElementById('progress-percentage');
    const steps = document.querySelectorAll('[data-step]');
    const nextBtn = document.querySelector('[data-hs-stepper-next-btn]');
    const backBtn = document.querySelector('[data-hs-stepper-back-btn]');
    const finishBtn = document.querySelector('[data-hs-stepper-finish-btn]');
    const contentItems = document.querySelectorAll('[data-hs-stepper-content-item]');
    
    let currentStep = 1;
    const totalSteps = steps.length;
    let isTransitioning = false;

    function updateProgress() {
        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        progressBar.style.width = `${progress}%`;
        progressLine.style.width = `${progress}%`;
        progressPercentage.textContent = `${Math.round(progress)}%`;
    }

    function updateStepStyles() {
        steps.forEach((step, index) => {
            const stepNumber = index + 1;
            const circle = step.querySelector('.step-circle');
            const text = step.querySelector('span:not(.step-circle)');

            if (stepNumber < currentStep) {
                // Completed step
                circle.classList.remove('bg-white', 'border-gray-200', 'text-gray-500');
                circle.classList.add('bg-red-800', 'border-red-800', 'text-white');
                text.classList.remove('text-gray-500');
                text.classList.add('text-gray-800');
                
                // Add check mark
                circle.innerHTML = '<i class="fas fa-check"></i>';
            } else if (stepNumber === currentStep) {
                // Current step
                circle.classList.remove('bg-white', 'border-gray-200', 'text-gray-500');
                circle.classList.add('bg-white', 'border-red-800', 'text-red-800');
                text.classList.remove('text-gray-500');
                text.classList.add('text-gray-800');
                circle.innerHTML = String(stepNumber).padStart(2, '0');
            } else {
                // Future step
                circle.classList.remove('bg-red-800', 'border-red-800', 'text-white');
                circle.classList.add('bg-white', 'border-gray-200', 'text-gray-500');
                text.classList.remove('text-gray-800');
                text.classList.add('text-gray-500');
                circle.innerHTML = String(stepNumber).padStart(2, '0');
            }
        });
    }

    function showCurrentContent() {
        if (isTransitioning) return;
        isTransitioning = true;

        contentItems.forEach((item, index) => {
            item.classList.remove('active');
        });

        const currentContent = contentItems[currentStep - 1];
        setTimeout(() => {
            currentContent.classList.add('active');
            isTransitioning = false;
        }, 50);
    }

    function updateButtons() {
        backBtn.disabled = currentStep === 1;
        backBtn.classList.toggle('opacity-50', currentStep === 1);
        
        if (currentStep === totalSteps) {
            nextBtn.style.display = 'none';
            finishBtn.style.display = 'inline-flex';
        } else {
            nextBtn.style.display = 'inline-flex';
            finishBtn.style.display = 'none';
        }
    }

    function updateStep(direction) {
        if (isTransitioning) return;

        if (direction === 'next' && currentStep < totalSteps) {
            currentStep++;
        } else if (direction === 'back' && currentStep > 1) {
            currentStep--;
        }

        updateProgress();
        updateStepStyles();
        showCurrentContent();
        updateButtons();
    }

    // Event listeners for navigation
    nextBtn.addEventListener('click', () => updateStep('next'));
    backBtn.addEventListener('click', () => updateStep('back'));

    // Initialize stepper
    updateProgress();
    updateStepStyles();
    showCurrentContent();
    updateButtons();

    // Add hover effects for steps
    steps.forEach(step => {
        const circle = step.querySelector('.step-circle');
        
        circle.addEventListener('mouseenter', () => {
            if (parseInt(step.dataset.step) >= currentStep) {
                circle.classList.add('transform', 'scale-110', 'shadow-lg');
            }
        });

        circle.addEventListener('mouseleave', () => {
            circle.classList.remove('transform', 'scale-110', 'shadow-lg');
        });
    });

    // Handle actividades selection
    const actividadSelect = document.getElementById('actividad');
    const actividadesContainer = document.getElementById('actividades-seleccionadas');
    const selectedActividades = new Set();

    actividadSelect?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value && !selectedActividades.has(selectedOption.value)) {
            selectedActividades.add(selectedOption.value);
            updateActividadesDisplay();
        }
    });

    function updateActividadesDisplay() {
        if (!actividadesContainer) return;
        
        if (selectedActividades.size === 0) {
            actividadesContainer.innerHTML = '<span class="text-gray-500 text-sm italic">Sin actividades seleccionadas</span>';
            return;
        }

        actividadesContainer.innerHTML = Array.from(selectedActividades).map(actividad => `
            <div class="flex items-center bg-gray-200 rounded-full px-3 py-1 text-sm">
                <span>${actividad}</span>
                <button class="ml-2 w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700 transition-all duration-200" 
                        onclick="removeActividad('${actividad}')">×</button>
            </div>
        `).join('');
    }

    window.removeActividad = function(actividad) {
        selectedActividades.delete(actividad);
        updateActividadesDisplay();
    }
}); 