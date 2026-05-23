/** @type {import('tailwindcss').Config}*/
const config = {
    content: ['./src/**/*.{html,js,svelte,ts}'],

    darkMode: 'class',

    theme: {
        extend: {
            colors: {

                "primary-dark": "#011627",
                "primary-light": "#FDFFFC",
                "primary-text-light": "#1d3510",
                "primary-text-dark": "#f2f2f2",

                "secondary-light": "#F71735",
                "secondary-dark": "F71735",
                "secondary-text-light": "black",
                "secondary-text-dark": "#f2f2f2",

                "tertiary-light": "#41EAD4",
                "tertiary-dark": "#41EAD4",
                "tertiary-text-light": "black",
                "tertiary-text-dark": "f2f2f2",

                "wekebio-green":"#60BF75ff",
                "wekebio-pastel":"#2CB34Aff",
                "wekebio-red":"#F22536ff",
                "wekebio-purple":"#5c4b9a",
                "wekebio-jade":"#58AA6Aff",

                "consolata-blue":"#0284c7",


                "accent-color":"#F71735",



            }
        }
    },

    plugins: []
};

module.exports = config;
