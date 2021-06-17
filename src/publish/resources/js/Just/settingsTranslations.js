module.exports = {
    methods: {
        /**
         * Translate the given key.
         */
        __(key, replace) {
            let translation;

            try {
                translation = key.split('.').reduce((t, i) => t[i] || null, window.settingsTranslations)
            } catch (e) {
                translation = key
            }

            _.forEach(replace, (value, key) => {
                translation = translation.replace(':' + key, value)
            })

            return translation;
        },

        locale(){
            return window.settingsTranslations.locale;
        }
    },
}

