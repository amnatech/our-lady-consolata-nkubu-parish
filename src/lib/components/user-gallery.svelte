<script>
  import { galleryStore } from '$lib/stores/gallery.js';
	import { onMount } from 'svelte';
  import Album from './album.svelte';
  import ImageUpload from './image-upload.svelte';
	import Segment from './segment.svelte';
	import { v4 } from 'uuid';
	import { current_liu } from '$lib/methods/methods';
	import { API_BASE_URL } from '$lib/config/base_urls';
	import { fetch_resource, post_resource } from '$lib/methods/functions';
  import pkg from 'notiflix';

  const { Notify, Confirm } = pkg;

  const liu=current_liu();
  
  let newAlbumTitle = '';
  let newAlbumDescription = '';
  let newAlbumDate = '';
  let showNewAlbumForm = false;
  let selectedAlbumId = null;
  let createAlbumLoading=false;
  
  $: albums = [];
  $:selectedAlbumId=null;
  $: previewImage = $galleryStore.previewImage;
  
  const createAlbum=async()=> {
    if (!newAlbumTitle.trim()) return;

    let url=`${API_BASE_URL}create-album.php`;

    let resource="albums";


    //create album
    let dt={
      album_id:v4(),
      title:newAlbumTitle,
      description:newAlbumDescription,
      created_by:liu.name,
      album_date:newAlbumDate
    }

    let headers={
      'Content-Type':'application/x-www-form-urlencoded'
    }

    try {

      createAlbumLoading=true;

      const RESPONSE=await post_resource(resource,url,dt,headers);

      const RES=RESPONSE.data;

      if(RES.success){
        Notify.success(RES.message);

        newAlbumTitle = '';
        newAlbumDescription = '';
        albums=await getAlbums();
        showNewAlbumForm = false;



      }else{
        Notify.failure(RES.message);
      }

      console.log(RES);

      createAlbumLoading=false;

      
    } catch (err) {
      console.log(err)
    }

  }

  const getAlbums=async()=>{

    let url=`${API_BASE_URL}gallery.php`;

    let resource="albums";

    try {

      const RESPONSE=await fetch_resource(resource,url);

      const RES=RESPONSE.data;

      return RES;
      
    } catch (err) {
      console.log(err);
    }

  }

  const refreshAlbums=async()=>{
       return await getAlbums();
  }
  
  const closePreview=() =>{
    // galleryStore.setPreviewImage(null);
  }



  onMount(async()=>{
    albums=await getAlbums();
  })
</script>

<Segment>

<div class="gallery my-4" slot="content">

  
  <!-- Main Content -->
  <div class="gallery-content">
    <!-- Albums Section -->
    <section class="albums-section">
      <div class="section-header">
        <h2>Albums ({albums.length})</h2>
        <button
          class="new-album-btn"
          onclick={() => showNewAlbumForm = !showNewAlbumForm}
        >
          {showNewAlbumForm ? 'Cancel' : '+ New Album'}
        </button>
      </div>
      
      <!-- New Album Form -->
      {#if showNewAlbumForm}
        <form class="new-album-form ui form" onsubmit={(e)=>{
          e.preventDefault();
          createAlbum();
        }}>
          <h3>Create New Album</h3>
          <div class="form-group">
            <input
              type="text"
              bind:value={newAlbumTitle}
              placeholder="Album title"
              class="form-input"
            />
          </div>

          <div class="form-group">
            <input
              type="date"
              bind:value={newAlbumDate}
              required
              placeholder="Album Date"
              class="form-input"
            />
          </div>
          <div class="form-group">
            <textarea
              bind:value={newAlbumDescription}
              required
              placeholder="Album description (optional)"
              class="form-textarea"
            ></textarea>
          </div>
          <div class="form-actions">
            <button
              type="submit"
              disabled={!newAlbumTitle.trim()}
              class="{createAlbumLoading?'ui  green mini button loading':"ui  green mini button"}"
            >
              Create Album
            </button>
          </div>
        </form>
      {/if}
      
      <!-- Albums List -->
      <div class="albums-list">
        {#each albums as album (album.album_id)}
          <!-- svelte-ignore a11y_click_events_have_key_events -->
          <!-- svelte-ignore a11y_no_static_element_interactions -->
          <div class="" onclick={()=>{
            selectedAlbumId=album.album_id
;          }}>
            <Album {album} on:refresh={async(e)=>{
              albums=await refreshAlbums();
            }} />
          </div>
        {:else}
          <div class="no-albums">
            <p>No albums yet. Create your first album!</p>
          </div>
        {/each}
      </div>
    </section>
    
    <!-- Upload Section -->
    {#if selectedAlbumId}
      <section class="upload-section">
        <div class="section-header">
          <h2>Upload to Album</h2>
          <div class="album-selector">
            <select bind:value={selectedAlbumId}>
              {#each albums as album (album.album_id)}
                <option value={album.album_id}>
                  {album.title} ({album.images.length})
                </option>
              {/each}
            </select>
          </div>
        </div>
        <ImageUpload albumId={selectedAlbumId} on:refresh={async(e)=>{
          albums=await refreshAlbums();
        }} />
      </section>
    {/if}
  </div>
  
  <!-- Image Preview Modal -->
  {#if previewImage}
    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div class="preview-modal" onclick={closePreview}>
      <div class="preview-content" onclick={(e)=>{
        e.stopPropagation();
      }}>
        <button class="close-preview" onclick={closePreview}>×</button>
        <img src={previewImage.url} alt="Preview" />
        <div class="preview-info">
          <h3>{previewImage.name}</h3>
          <p>Size: {(previewImage.size / 1024 / 1024).toFixed(2)} MB</p>
          <p>Type: {previewImage.type}</p>
          {#if previewImage.filters}
            <p class="edited-badge">Edited</p>
          {/if}
        </div>
      </div>
    </div>
  {/if}
</div>
</Segment>


<style>
  .gallery {
    min-height: 100vh;

  }
  
  .gallery-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    display: grid;
    gap: 40px;
  }
  
  @media (min-width: 768px) {
    .gallery-content {
      grid-template-columns: 1fr 1fr;
    }
  }
  
  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
  }
  
  .section-header h2 {
    margin: 0;
    font-size: 24px;
  }
  
  .new-album-btn {
    padding: 10px 20px;
    background: #4CAF50;

    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
  }
  
  .new-album-btn:hover {
    background: #45a049;
  }
  
  .new-album-form {
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 24px;
  }
  
  .new-album-form h3 {
    margin: 0 0 16px 0;
    font-size: 18px;
  }
  
  .form-group {
    margin-bottom: 16px;
  }
  
  .form-input, .form-textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #555;
    border-radius: 4px;
    font-size: 14px;
  }
  
  .form-textarea {
    min-height: 80px;
    resize: vertical;
  }
  
  .form-actions {
    display: flex;
    justify-content: flex-end;
  }
  
  
  .albums-list {
    margin-top: 20px;
  }
  
  .no-albums {
    text-align: center;
    padding: 40px 20px;
    color: #888;
    background: #2a2a2a;
    border-radius: 8px;
  }
  
  .album-selector select {
    padding: 8px 16px;
    background: #333;
    border: 1px solid #555;
    border-radius: 4px;
    font-size: 14px;
  }
  
  .preview-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    animation: fadeIn 0.3s ease;
  }
  
  .preview-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
    border-radius: 8px;
    overflow: hidden;
    animation: slideUp 0.3s ease;
  }
  
  .close-preview {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    z-index: 1001;
  }
  
  .preview-content img {
    display: block;
    max-width: 100%;
    max-height: 70vh;
    margin: 0 auto;
  }
  
  .preview-info {
    padding: 20px;
  }
  
  .preview-info h3 {
    margin: 0 0 8px 0;

  }
  
  .preview-info p {
    margin: 4px 0;
    font-size: 14px;
  }
  
  .edited-badge {
    display: inline-block;
    padding: 4px 8px;
    background: #4CAF50;
    color: white;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>