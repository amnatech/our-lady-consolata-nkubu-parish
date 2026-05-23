<script>
  import { galleryStore } from '$lib/stores/gallery.js';
  import ImageEditor from '$lib/components/image-editor.svelte';
	import { post_resource } from '$lib/methods/functions';
	import { createEventDispatcher } from 'svelte';
  import pkg from 'notiflix';
	import { current_liu } from '$lib/methods/methods';
	import { onMount } from 'svelte';
	import { API_BASE_URL } from '$lib/config/base_urls';

  const { Notify, Confirm } = pkg;

  const liu=current_liu();
  const dispatch=createEventDispatcher();
  
  
  export let albumId = null;

  const resfreshAlbums=()=>{
    dispatch('refresh',{selectedAlbum:albumId});
  }
  
  
  let isDragging = false;
  let selectedFiles = [];
  let isUploading = false;
  let uploadProgress = 0;
  let showEditor = false;
  let currentImageForEdit = null;
  let editedImages = [];
  
  function handleDragOver(e) {
    e.preventDefault();
    isDragging = true;
  }
  
  function handleDragLeave(e) {
    e.preventDefault();
    isDragging = false;
  }
  
  function handleDrop(e) {
    e.preventDefault();
    isDragging = false;
    
    const files = Array.from(e.dataTransfer.files).filter(file => 
      file.type.startsWith('image/')
    );
    
    handleFiles(files);
  }
  
  function handleFileSelect(e) {
    const files = Array.from(e.target.files).filter(file => 
      file.type.startsWith('image/')
    );
    
    handleFiles(files);
  }
  
  function handleFiles(files) {
    selectedFiles = [...selectedFiles, ...files];
  }
  
  function removeFile(index) {
    selectedFiles = selectedFiles.filter((_, i) => i !== index);
  }
  
  function editImage(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      currentImageForEdit = e.target.result;
      showEditor = true;
    };
    reader.readAsDataURL(file);
  }
  
  const handleEditSave=(editedImage)=> {
    editedImages.push(editedImage);
    showEditor = false;
    currentImageForEdit = null;
  }
  
  const uploadImages =async()=> {
    if (!albumId || selectedFiles.length === 0) return;
    
    isUploading = true;
    uploadProgress = 0;
    
    const uploadedImages = [];

    let fd=new FormData();

    const url= `${API_BASE_URL}upload-gallery-image.php`;

    const resource="albums";

    fd.append('album_id',albumId);
    
    // Simulate upload process
    for (let i = 0; i < selectedFiles.length; i++) {
      const file = selectedFiles[i];
      
      // Check if this file has been edited
      const editedImage = editedImages.find(img => 
        img.originalUrl && img.originalUrl.includes(file.name)
      );

      // 
      
      if (editedImage) {
        // Use edited image
        uploadedImages.push({
          url: editedImage.url,
          name: file.name,
          size: editedImage.metadata.size,
          type: editedImage.metadata.type,
          filters: editedImage.filters,
          metadata: editedImage.metadata
        });


      fd.append(file.name,editedImage);


      } else {
        // Use original image
        const imageUrl = URL.createObjectURL(file);
        uploadedImages.push({
          url: imageUrl,
          name: file.name,
          size: file.size,
          type: file.type,
          filters: null,
          metadata: {
            width: 0,
            height: 0
          }
        });

        fd.append(file.name,file);

      }

      // Update progress
      // uploadProgress = ((i + 1) / selectedFiles.length) * 100;
      await new Promise(resolve => setTimeout(resolve, 300)); // Simulate network delay

    }


    try {

         console.log(fd);

         let headers={'Content-Type':'multipart/form-data'}


        isUploading=true;

        const RESPONSE=await post_resource(resource,url,fd,headers);

        const RES=RESPONSE.data;

        if(RES.success){
          Notify.success(RES.message);


        }else{
          Notify.failure(RES.message);
        }

        console.log(RES);

        isUploading=false;

      
    } catch (err) {
      console.log(err)
    }

    
    // Add images to album
    galleryStore.addImagesToAlbum(albumId, uploadedImages);
    
    // Reset state
    selectedFiles = [];
    editedImages = [];
    isUploading = false;
    uploadProgress = 0;
  }

  onMount(()=>{
    console.log(albumId);
  })
</script>

<div class="image-upload">
  {#if showEditor}
    <ImageEditor
      image={currentImageForEdit}
      onSave={handleEditSave}
      onCancel={() => showEditor = false}
    />
  {:else}
    <!-- Upload Area -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div
      class="upload-area {isDragging ? 'dragging' : ''}"
      on:dragover={handleDragOver}
      on:dragleave={handleDragLeave}
      on:drop={handleDrop}
    >
      <div class="upload-content">
        <svg class="upload-icon" viewBox="0 0 24 24">
          <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
        </svg>
        <p>Drag & drop images here</p>
        <p class="upload-hint">or</p>
        <label class="file-input-label">
          <input
            type="file"
            accept="image/*"
            multiple
            on:change={handleFileSelect}
            style="display: none;"
          />
          Browse Files
        </label>
        <p class="file-types">Supports: JPG, PNG, GIF, WebP</p>
      </div>
    </div>
    
    <!-- Selected Files -->
    {#if selectedFiles.length > 0}
      <div class="selected-files">
        <h3>Selected Files ({selectedFiles.length})</h3>
        <div class="file-list">
          {#each selectedFiles as file, index (index)}
            <div class="file-item">
              <div class="file-info">
                <span class="file-name">{file.name}</span>
                <span class="file-size">({(file.size / 1024 / 1024).toFixed(2)} MB)</span>
              </div>
              <div class="file-actions">
                <button
                  class="edit-btn"
                  on:click={() => editImage(file)}
                  title="Edit before upload"
                >
                  Edit
                </button>
                <button
                  class="remove-btn"
                  on:click={() => removeFile(index)}
                  title="Remove"
                >
                  ×
                </button>
              </div>
            </div>
          {/each}
        </div>
        
        <!-- Upload Progress -->
        {#if isUploading}
          <div class="upload-progress">
            <div class="progress-bar">
              <div
                class="progress-fill"
                style="width: {uploadProgress}%"
              ></div>
            </div>
            <div class="progress-text">
              Uploading... {Math.round(uploadProgress)}%
            </div>
          </div>
        {:else}
          <div class="upload-actions">
            <button
              class="upload-btn"
              on:click={uploadImages}
              disabled={!albumId}
            >
              {albumId ? `Upload to Album` : 'Select an album first'}
            </button>
            <button
              class="clear-btn"
              on:click={() => selectedFiles = []}
            >
              Clear All
            </button>
          </div>
        {/if}
      </div>
    {/if}
    
    <!-- Edited Images Preview -->
    {#if editedImages.length > 0}
      <div class="edited-images">
        <h3>Edited Images ({editedImages.length})</h3>
        <div class="edited-grid">
          {#each editedImages as editedImage, index (index)}
            <div class="edited-item">
              <img src={editedImage.url} alt="Edited" />
              <div class="edited-overlay">
                <button
                  class="remove-edited"
                  on:click={() => editedImages.splice(index, 1)}
                >
                  Remove
                </button>
              </div>
            </div>
          {/each}
        </div>
      </div>
    {/if}
  {/if}
</div>

<style>
  .image-upload {
    width: 100%;
  }
  
  .upload-area {
    border: 2px dashed #555;
    border-radius: 8px;
    padding: 60px 20px;
    text-align: center;
    transition: all 0.3s ease;
    background: #2a2a2a;
    margin-bottom: 20px;
  }
  
  .upload-area.dragging {
    border-color: #4CAF50;
    background: rgba(76, 175, 80, 0.1);
  }
  
  .upload-content {
    color: #888;
  }
  
  .upload-icon {
    width: 64px;
    height: 64px;
    fill: #555;
    margin-bottom: 20px;
  }
  
  .upload-area.dragging .upload-icon {
    fill: #4CAF50;
  }
  
  .upload-hint {
    margin: 10px 0;
    color: #666;
  }
  
  .file-input-label {
    display: inline-block;
    padding: 10px 24px;
    background: #4CAF50;
    color: white;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s ease;
  }
  
  .file-input-label:hover {
    background: #45a049;
  }
  
  .file-types {
    margin-top: 10px;
    font-size: 12px;
    color: #666;
  }
  
  .selected-files {
    background: #2a2a2a;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
  }
  
  .selected-files h3 {
    margin: 0 0 16px 0;
    color: #fff;
    font-size: 18px;
  }
  
  .file-list {
    max-height: 300px;
    overflow-y: auto;
    margin-bottom: 20px;
  }
  
  .file-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #333;
    border-radius: 4px;
    margin-bottom: 8px;
  }
  
  .file-info {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .file-name {
    color: #fff;
    font-size: 14px;
  }
  
  .file-size {
    color: #888;
    font-size: 12px;
  }
  
  .file-actions {
    display: flex;
    gap: 8px;
  }
  
  .edit-btn, .remove-btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
  }
  
  .edit-btn {
    background: #2196F3;
    color: white;
  }
  
  .edit-btn:hover {
    background: #0b7dda;
  }
  
  .remove-btn {
    background: #f44336;
    color: white;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .upload-progress {
    margin-top: 20px;
  }
  
  .progress-bar {
    width: 100%;
    height: 6px;
    background: #444;
    border-radius: 3px;
    overflow: hidden;
  }
  
  .progress-fill {
    height: 100%;
    background: #4CAF50;
    transition: width 0.3s ease;
  }
  
  .progress-text {
    margin-top: 8px;
    color: #888;
    font-size: 14px;
    text-align: center;
  }
  
  .upload-actions {
    display: flex;
    gap: 12px;
    margin-top: 20px;
  }
  
  .upload-btn, .clear-btn {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
  }
  
  .upload-btn {
    background: #4CAF50;
    color: white;
  }
  
  .upload-btn:disabled {
    background: #666;
    cursor: not-allowed;
  }
  
  .clear-btn {
    background: #666;
    color: white;
  }
  
  .clear-btn:hover {
    background: #777;
  }
  
  .edited-images {
    background: #2a2a2a;
    border-radius: 8px;
    padding: 20px;
  }
  
  .edited-images h3 {
    margin: 0 0 16px 0;
    color: #fff;
    font-size: 18px;
  }
  
  .edited-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 10px;
  }
  
  .edited-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 4px;
    overflow: hidden;
  }
  
  .edited-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .edited-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  
  .edited-item:hover .edited-overlay {
    opacity: 1;
  }
  
  .remove-edited {
    padding: 6px 12px;
    background: #f44336;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
  }
</style>