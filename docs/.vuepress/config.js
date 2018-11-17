module.exports = {
    title: 'HubSpot',
    description: 'Integration Plugin for Craft CMS',
    base: '/',
    theme: 'flipbox',
    themeConfig: {
        logo: '/icon.svg',
        docsRepo: 'flipboxfactory/craft-hubspot',
        docsDir: 'docs',
        docsBranch: 'master',
        editLinks: true,
        search: true,
        searchMaxSuggestions: 10,
        codeLanguages: {
            twig: 'Twig',
            php: 'PHP',
            json: 'JSON',
            // any other languages you want to include in code toggles...
        },
        nav: [
            {text: 'Details', link: 'https://www.flipboxdigital.com/craft-cms-plugins/hubspot'},
            {text: 'Changelog', link: 'https://github.com/flipboxfactory/craft-hubspot/blob/master/CHANGELOG.md'},
            {text: 'Repo', link: 'https://github.com/flipboxfactory/craft-hubspot'}
        ],
        sidebar: {
            '/': [
                {
                    title: 'Getting Started',
                    collapsable: false,
                    children: [
                        ['/', 'Introduction'],
                        ['installation', 'Installation / Upgrading'],
                        'support'
                    ]
                }
            ]
        }
    },
    markdown: {
        anchor: { level: [2, 3, 4] },
        toc: { includeLevel: [3] },
        config(md) {
            md.use(require('vuepress-theme-flipbox/markup'))
        }
    }
}