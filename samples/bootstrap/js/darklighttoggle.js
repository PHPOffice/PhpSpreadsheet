document.addEventListener('DOMContentLoaded', () => {
    const toggler = document.getElementById('themeToggler');
    const icon = document.getElementById('themeIcon');
    const htmlElement = document.documentElement;

    function updateIcon(theme)
    {
        if (theme === 'dark') {
            icon.className = 'fa fa-sun-o fa-lg'; // Sun icon for dark mode
        } else {
            icon.className = 'fa fa-moon-o fa-lg'; // Moon icon for light mode
        }
    }

    // 1. Define a helper function to get the correct initial theme
    function getPreferredTheme()
    {
        // Check localStorage first
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            return savedTheme;
        }

        // If empty, fall back to checking the system preference
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        return systemPrefersDark ? 'dark' : 'light';
    }

    // 2. Initialize the page layout based on the computed preference
    const initialTheme = getPreferredTheme();
    htmlElement.setAttribute('data-bs-theme', initialTheme);
    updateIcon(initialTheme);

    // 3. Handle manual clicks on the toggler button
    toggler.addEventListener('click', () => {
        const currentTheme = htmlElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        htmlElement.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateIcon(newTheme);
    });

    /*
    // 4. Optional: Listen for system changes in real-time
    // If the user flips their OS theme setting, the site updates live (unless they overrode it)
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem('theme')) {
            const updatedTheme = e.matches ? 'dark' : 'light';
            htmlElement.setAttribute('data-bs-theme', updatedTheme);
            updateIcon(updatedTheme);
        }
    });
    */
});
