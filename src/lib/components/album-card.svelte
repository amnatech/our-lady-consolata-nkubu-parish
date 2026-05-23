<script>
	import { API_BASE_URL } from '$lib/config/base_urls';
	import { post_resource } from '$lib/methods/functions';
  import pkg from 'notiflix';
	import { createEventDispatcher } from 'svelte';

  const { Notify, Confirm } = pkg;

  const URL=`${API_BASE_URL}gallery.php`;

  const RESOURCE="Albums";


  const dispatch=createEventDispatcher();
  
  export let album;
  
  let isExpanded = false;
  let isEditing = false;
  let editTitle = '';
  let editDescription = '';

  let savingEdit=false;

  const resfreshAlbums=()=>{
    dispatch('refresh',{selectedAlbum:album.album_id});
  }
  
  const toggleExpand=() =>{
    isExpanded = !isExpanded;
    if (isExpanded) {
      galleryStore.selectAlbum(album.id);
    }
  }

  
  const getImageName=(imageUrl)=>{

    let pathArr=imageUrl.split('/');

    return pathArr[pathArr.length-1];
  }

  const expandImage=()=>{

  }

</script>

<div class="album shadow-lg {isExpanded ? 'expanded' : ''}">
  <!-- Album Header -->
  <!-- svelte-ignore a11y_click_events_have_key_events -->
  <!-- svelte-ignore a11y_no_static_element_interactions -->
  <div class="album-header" on:click={toggleExpand}>
    <div class="album-image p-2">
        <img class="w-26 h-18" src="{API_BASE_URL}{album?.images[0]}" alt="{album.title}">
    </div>
    <div class="album-info">
        <h3 class="album-title">{album.title}</h3>
        <p class="album-description">{album.description}</p>
        <div class="album-meta">
          <span class="image-count">{album.images.length} images</span>
          <span class="album-date">
            Updated: {new Date(album.updated_at).toLocaleDateString()}
          </span>
        </div>
      </div>
      
      <div class="album-actions" on:click|stopPropagation>
        <div class="expand-icon">
          {#if isExpanded}▼{:else}▶{/if}
        </div>
    </div>
  </div>
  
  <!-- Album Content (Expanded) -->
  {#if isExpanded}
    <div class="album-content">
      <!-- Image Grid -->
      {#if album.images.length > 0}
        <div class="image-grid">
          {#each album.images as imageUrl,i}
            <div class="image-item">
              <!-- svelte-ignore a11y_click_events_have_key_events -->
              <!-- svelte-ignore a11y_no_noninteractive_element_interactions -->
              <img
                src={`${API_BASE_URL}${imageUrl}`}
                alt={imageUrl}
                class="thumbnail"
                on:click={() => galleryStore.setPreviewImage(imageUrl)}
              />
              <div class="image-overlay">
                <button
                  class="delete-image-btn"
                  on:click={() => {
                        expandImage(imageUrl)
                  }}
                  title="Delete image"
                >
                  <i class="ri-fullscreen-line"></i>
                </button>
              </div>
            </div>
          {/each}
        </div>
      {:else}
        <div class="empty-album">
          <p>No images in this album yet.</p>
        </div>
      {/if}
    </div>
  {/if}
</div>

<style>
  .album {
    background: #f0f0f0;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 16px;
    transition: all 0.3s ease;
  }
  
  .album.expanded {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  }
  
  .album-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  
  .album-header:hover {
    background: #d0d0d0;
  }
  
  .album-info {
    flex: 1;
  }
  
  .album-title {
    margin: 0 0 8px 0;
    color: #1a1a1a;
    font-size: 20px;
  }
  
  .album-description {
    margin: 0 0 12px 0;
    color: #7a7a7a;
    font-size: 14px;
  }
  
  .album-meta {
    display: flex;
    gap: 16px;
  }
  
  .image-count, .album-date {
    color: #888;
    font-size: 12px;
  }
  
  .album-actions {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .expand-icon {
    color: #888;
    font-size: 12px;
    margin-left: 8px;
  }
  
 
  .album-content {
    padding: 20px;
    border-top: 1px solid #444;
  }
  
  .image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 16px;
  }
  
  .image-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 4px;
    overflow: hidden;
  }
  
  .thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }
  
  .thumbnail:hover {
    transform: scale(1.05);
  }
  
  .image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: flex-start;
    justify-content: flex-end;
    opacity: 0;
    transition: opacity 0.3s ease;
    padding: 8px;
  }
  
  .image-item:hover .image-overlay {
    opacity: 1;
  }
  
  .delete-image-btn {
    width: 24px;
    height: 24px;
    background: #f44336;
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .empty-album {
    text-align: center;
    padding: 40px 20px;
    color: #888;
  }
  
  .empty-album p {
    margin: 0 0 8px 0;
  }
</style>