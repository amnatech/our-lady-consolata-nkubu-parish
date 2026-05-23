import CONTACTS from "./contacts";

export const mainNavLinks = [
    { name: 'Home', path: '/' },
    { name: 'About Us', path: '/about-us' },
    { name: 'Activities', path: '/activities' },
    { name: 'News & Events', path: '/events' },
    { name: 'Gallery', path: '/gallery' },
    { name: 'Projects', path: '/projects' },
    { name: 'Contact Us', path: '/contact-us' },
    { name: 'Login', path: '/account/login' }
];

export const adminNavLinks = [
    { name: 'Dashboard', path: '/admin/dash',icon:'<i class="ri-dashboard-line"></i>' },
    { name: 'Prayer Houses', path: '/admin/houses',icon:'<i class="ri-cross-line"></i>' },
    { name: 'Groups', path: '/admin/groups',icon:'<i class="ri-user-line"></i>' },
    { name: 'Activities', path: '/admin/activities', icon:'<i class="ri-tree-line"></i>' },
    { name: 'News $ Events', path: '/admin/news-and-events',icon:'<i class="ri-file-list-3-line"></i>' },
    { name: 'Gallery', path: '/admin/gallery',icon:'<i class="ri-gallery-view"></i>' },
    { name: 'Messages', path: '/admin/messages' ,icon:'<i class="ri-mail-line"></i>'},
    { name: 'Projects', path: '/admin/projects',icon:'<i class="ri-trello-line"></i>' },
    { name: 'Users', path: '/admin/users',icon:'<i class="ri-group-line"></i>' },
    { name: 'Profile', path: '/admin/profile',icon:'<i class="ri-profile-line"></i>' },
    { name: 'Settings', path: '/admin/settings',icon:'<i class="ri-settings-line"></i>' },
    { name: 'Logout', path: '/admin/logout',icon:'<i class="ri-logout-circle-r-line"></i>' }
];

export const houseNavLinks = [
    { name: 'Dashboard', path: '/prayer-house/dash',icon:'<i class="ri-dashboard-line"></i>' },
    { name: 'Activities', path: '/prayer-house/activities', icon:'<i class="ri-tree-line"></i>' },
    { name: 'News $ Events', path: '/prayer-house/news-and-events',icon:'<i class="ri-file-list-3-line"></i>' },
    { name: 'Gallery', path: '/prayer-house/gallery',icon:'<i class="ri-gallery-view"></i>' },
    { name: 'Messages', path: '/prayer-house/messages' ,icon:'<i class="ri-mail-line"></i>'},
    { name: 'Projects', path: '/prayer-house/projects',icon:'<i class="ri-trello-line"></i>' },
    { name: 'Users', path: '/prayer-house/users',icon:'<i class="ri-group-line"></i>' },
    { name: 'Profile', path: '/prayer-house/profile',icon:'<i class="ri-profile-line"></i>' },

    { name: 'Settings', path: '/prayer-house/settings',icon:'<i class="ri-settings-line"></i>' },

    { name: 'Logout', path: '/prayer-house/logout',icon:'<i class="ri-logout-circle-r-line"></i>' }
];


export const userNavLinks = [
    { name: 'Dashboard', path: '/user/dash',icon:'<i class="ri-dashboard-line"></i>' },
    { name: 'Activities', path: '/user/activities', icon:'<i class="ri-tree-line"></i>' },
    { name: 'News $ Events', path: '/user/news-and-events',icon:'<i class="ri-file-list-3-line"></i>' },
    { name: 'Gallery', path: '/user/gallery',icon:'<i class="ri-gallery-view"></i>' },
    { name: 'Messages', path: '/user/messages' ,icon:'<i class="ri-mail-line"></i>'},
    { name: 'Projects', path: '/user/projects',icon:'<i class="ri-trello-line"></i>' },
    { name: 'Users', path: '/user/users',icon:'<i class="ri-group-line"></i>' },
    { name: 'Profile', path: '/user/profile',icon:'<i class="ri-profile-line"></i>' },

    { name: 'Settings', path: '/user/settings',icon:'<i class="ri-settings-line"></i>' },

    { name: 'Logout', path: '/user/logout',icon:'<i class="ri-logout-circle-r-line"></i>' }
];

// Social media data
export const socialMedia = [
    { name: 'Facebook', icon: '<i class="ri-facebook-circle-line"></i>',icon_name:"facebook", url: 'https://facebook.com', color: '#1877F2' },
    { name: 'Twitter/X', icon: '<i class="ri-twitter-x-line"></i>',icon_name:"twitter",  url: 'https://twitter.com', color: '#1DA1F2' },
    { name: 'Instagram', icon: '<i class="ri-instagram-line"></i>', icon_name:"instagram", url: 'https://instagram.com', color: '#E4405F' },
    { name: 'LinkedIn', icon: '<i class="ri-linkedin-line"></i>',icon_name:"linkedin",  url: 'https://linkedin.com', color: '#0A66C2' },
    { name: 'YouTube', icon: '<i class="ri-youtube-line"></i>',icon_name:"youtube",  url: 'https://youtube.com', color: '#FF0000' },
    { name: 'Email', icon: '<i class="ri-mail-line"></i>',icon_name:"email",  url: '#', color: '#FF0000' }

];

// Quick links data
export const quickLinks = [
    { name: 'Home', url: '/' },
    { name: 'About Us', url: '/about' },
    { name: 'Services', url: '/services' },
    { name: 'Portfolio', url: '/portfolio' },
    { name: 'Blog', url: '/blog' },
    { name: 'Contact', url: '/contact' }
];


// Resources data
export const resources = [
    { name: 'Documentation', url: '/docs' },
    { name: 'Help Center', url: '/help' },
    { name: 'Community', url: '/community' },
    { name: 'Tutorials', url: '/tutorials' },
    { name: 'API Reference', url: '/api' },
    { name: 'Privacy Policy', url: '/privacy' }
];

// Logo tab data
export const logoInfo = {
    src: 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?q=80&w=200&auto=format&fit=crop',
    alt: 'Company Logo',
    name: CONTACTS.name,
    tagline: CONTACTS.tagline,
    description:CONTACTS.description
};