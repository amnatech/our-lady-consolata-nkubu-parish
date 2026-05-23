import { writable } from 'svelte/store';
import { browser } from '$app/environment';

// Load from localStorage if available
const loadFromStorage = (key, defaultValue) => {
  if (browser) {
    const stored = localStorage.getItem(key);
    return stored ? JSON.parse(stored) : defaultValue;
  }
  return defaultValue;
};

// Create initial state
const createGalleryStore = () => {
  const initialState = loadFromStorage('gallery_albums', {
    albums: [
      {
        id: '1',
        title: 'Vacation Photos',
        description: 'Summer vacation 2023',
        coverImage: null,
        images: [],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      },
      {
        id: '2',
        title: 'Family Events',
        description: 'Family gatherings and celebrations',
        coverImage: null,
        images: [],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      }
    ],
    selectedAlbumId: null,
    editingAlbumId: null,
    previewImage: null
  });

  const { subscribe, update, set } = writable(initialState);

  // Save to localStorage on changes
  if (browser) {
    subscribe(state => {
      localStorage.setItem('gallery_albums', JSON.stringify(state));
    });
  }

  return {
    subscribe,
    // Album actions
    createAlbum: (album) => update(state => {
      const newAlbum = {
        ...album,
        id: Date.now().toString(),
        images: [],
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      };
      return {
        ...state,
        albums: [...state.albums, newAlbum],
        editingAlbumId: newAlbum.id
      };
    }),
    
    updateAlbum: (albumId, updates) => update(state => ({
      ...state,
      albums: state.albums.map(album => 
        album.id === albumId 
          ? { ...album, ...updates, updatedAt: new Date().toISOString() }
          : album
      )
    })),
    
    deleteAlbum: (albumId) => update(state => ({
      ...state,
      albums: state.albums.filter(album => album.id !== albumId),
      selectedAlbumId: state.selectedAlbumId === albumId ? null : state.selectedAlbumId
    })),
    
    selectAlbum: (albumId) => update(state => ({
      ...state,
      selectedAlbumId: albumId
    })),
    
    setEditingAlbum: (albumId) => update(state => ({
      ...state,
      editingAlbumId: albumId
    })),
    
    // Image actions
    addImagesToAlbum: (albumId, images) => update(state => ({
      ...state,
      albums: state.albums.map(album => {
        if (album.id === albumId) {
          const newImages = images.map(img => ({
            ...img,
            id: Date.now() + Math.random().toString(36).substr(2, 9),
            uploadedAt: new Date().toISOString(),
            metadata: img.metadata || {}
          }));
          return {
            ...album,
            images: [...album.images, ...newImages],
            updatedAt: new Date().toISOString(),
            coverImage: album.coverImage || newImages[0]?.url
          };
        }
        return album;
      })
    })),
    
    updateImage: (albumId, imageId, updates) => update(state => ({
      ...state,
      albums: state.albums.map(album => {
        if (album.id === albumId) {
          return {
            ...album,
            images: album.images.map(img => 
              img.id === imageId ? { ...img, ...updates } : img
            ),
            updatedAt: new Date().toISOString()
          };
        }
        return album;
      })
    })),
    
    deleteImage: (albumId, imageId) => update(state => ({
      ...state,
      albums: state.albums.map(album => {
        if (album.id === albumId) {
          const filteredImages = album.images.filter(img => img.id !== imageId);
          return {
            ...album,
            images: filteredImages,
            updatedAt: new Date().toISOString(),
            coverImage: album.coverImage === imageId ? 
              (filteredImages[0]?.url || null) : album.coverImage
          };
        }
        return album;
      })
    })),
    
    setPreviewImage: (image) => update(state => ({
      ...state,
      previewImage: image
    })),
    
    reset: () => set({
      albums: [],
      selectedAlbumId: null,
      editingAlbumId: null,
      previewImage: null
    })
  };
};

export const galleryStore = createGalleryStore();