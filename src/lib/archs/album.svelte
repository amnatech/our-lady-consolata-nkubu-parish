<script>
	import { API_BASE_URL } from '$lib/config/base_urls';
	import { post_resource } from '$lib/methods/functions';
	import { current_liu } from '$lib/methods/methods';
  import { galleryStore } from '$lib/stores/gallery.js';

  import pkg from 'notiflix';
	import { createEventDispatcher } from 'svelte';

  const { Notify, Confirm } = pkg;

  const liu=current_liu();

  const dispatch=createEventDispatcher();
  
  export let album;
  
  let isExpanded = false;
  let isEditing = false;
  let editTitle = '';
  let editDescription = '';

  const resfreshAlbums=()=>{
    dispatch('refresh',{selectedAlbum:album.album_id});
  }
  
  const toggleExpand=() =>{
    isExpanded = !isExpanded;
    if (isExpanded) {
      galleryStore.selectAlbum(album.id);
    }
  }
  
  const startEdit=()=> {
    editTitle = album.title;
    editDescription = album.description;
    isEditing = true;
  }
  
  const saveEdit=()=> {
    galleryStore.updateAlbum(album.id, {
      title: editTitle,
      description: editDescription
    });
    isEditing = false;
  }
  
  const cancelEdit=()=> {
    isEditing = false;
  }
  
  const deleteAlbum=async() =>{
    if (confirm(`Delete album "${album.title}"? This cannot be undone.`)) {
      galleryStore.deleteAlbum(album.id);
    }
  }

  const getImageName=(imageUrl)=>{

    let pathArr=imageUrl.split('/');

    return pathArr[pathArr.length-1];
  }
  
  const deleteImage=async(imageUrl)=> {

    let imageName=getImageName(imageUrl);

    let dt={
      action:"delete_album_image",
      deleted_by:liu.name,
      image_url:imageUrl,
      album_id:album.album_id,
      image_name:imageName
    }

    try {
      let headers={
        'Content-Type':'application/x-www-form-urlencoded'
      }

      let url=`${API_BASE_URL}gallery.php`;

      let resource="Albums";

      const RESPONSE=await post_resource(resource,url,dt,headers);

      const RES=RESPONSE.data;

      if(RES.success){
        Notify.success(RES.message);
      }else{
        Notify.failure(RES.message);

      }

      console.log(RES);

    } catch (err) {
      console.log(err);
    }
    console.log(imageUrl);
  }
</script>

<div class="album {isExpanded ? 'expanded' : ''}">
  <!-- Album Header -->
  <!-- svelte-ignore a11y_click_events_have_key_events -->
  <!-- svelte-ignore a11y_no_static_element_interactions -->
  <div class="album-header" on:click={toggleExpand}>
    {#if isEditing}
      <div class="album-edit-form" on:click|stopPropagation>
        <input
          type="text"
          bind:value={editTitle}
          placeholder="Album title"
          class="edit-title"
        />
        <textarea
          bind:value={editDescription}
          placeholder="Album description"
          class="edit-description"
        ></textarea>
        <div class="edit-actions">
          <button on:click|stopPropagation={saveEdit}>Save</button>
          <button on:click|stopPropagation={cancelEdit}>Cancel</button>
        </div>
      </div>
    {:else}
      <div class="album-info">
        <h3 class="album-title">{album.title}</h3>
        <p class="album-description">{album.description}</p>
        <div class="album-meta">
          <span class="image-count">{album.images.length} images</span>
          <span class="album-date">
            Updated: {new Date(album.updatedAt).toLocaleDateString()}
          </span>
        </div>
      </div>
      
      <div class="album-actions" on:click|stopPropagation>
        <button
          class="edit-btn"
          on:click={startEdit}
          title="Edit album"
        >
          Edit
        </button>
        <button
          class="delete-btn"
          on:click={deleteAlbum}
          title="Delete album"
        >
          Delete
        </button>
        <div class="expand-icon">
          {#if isExpanded}▼{:else}▶{/if}
        </div>
      </div>
    {/if}
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
                          Confirm.show(
                            "Delete Album Image",
                            `Delete Album Image ${imageUrl}?`,
                            "Yes",
                            "No",
                            () => {
                                deleteImage(imageUrl)
                               
                            },
                            () => {
                                //    do nothing
                            },
                            {}
                         );
                  }}
                  title="Delete image"
                >
                  ×
                </button>
              </div>
            </div>
          {/each}
        </div>
      {:else}
        <div class="empty-album">
          <p>No images in this album yet.</p>
          <p>Upload some images to get started!</p>
        </div>
      {/if}
    </div>
  {/if}
</div>

<style>
  .album {
    background: #2a2a2a;
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
    background: #333;
  }
  
  .album-info {
    flex: 1;
  }
  
  .album-title {
    margin: 0 0 8px 0;
    color: #fff;
    font-size: 20px;
  }
  
  .album-description {
    margin: 0 0 12px 0;
    color: #ccc;
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
  
  .edit-btn, .delete-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
  }
  
  .edit-btn {
    background: #2196F3;
    color: white;
  }
  
  .edit-btn:hover {
    background: #0b7dda;
  }
  
  .delete-btn {
    background: #f44336;
    color: white;
  }
  
  .delete-btn:hover {
    background: #d32f2f;
  }
  
  .expand-icon {
    color: #888;
    font-size: 12px;
    margin-left: 8px;
  }
  
  .album-edit-form {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  
  .edit-title {
    padding: 8px 12px;
    background: #333;
    border: 1px solid #555;
    border-radius: 4px;
    color: #fff;
    font-size: 18px;
  }
  
  .edit-description {
    padding: 8px 12px;
    background: #333;
    border: 1px solid #555;
    border-radius: 4px;
    color: #fff;
    font-size: 14px;
    min-height: 60px;
    resize: vertical;
  }
  
  .edit-actions {
    display: flex;
    gap: 8px;
  }
  
  .edit-actions button {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
  }
  
  .edit-actions button:first-child {
    background: #4CAF50;
    color: white;
  }
  
  .edit-actions button:last-child {
    background: #666;
    color: white;
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