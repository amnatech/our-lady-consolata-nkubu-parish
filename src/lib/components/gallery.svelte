<script>
	import { onMount } from 'svelte';
    import ImageUpload from './image-upload.svelte';
	import Segment from './segment.svelte';
	import { v4 } from 'uuid';
	import { API_BASE_URL } from '$lib/config/base_urls';
	import { fetch_resource, post_resource } from '$lib/methods/functions';
    import pkg from 'notiflix';
	import AlbumCard from './album-card.svelte';
	import Title from './title.svelte';

  const { Notify, Confirm } = pkg;

  export let title="Our Gallery Albums";

  export let subtitle="View gallery album photos";

  
  $: albums = [];
  $:selectedAlbumId=null;
  $: previewImage = null;
  

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


  const closePreview=() =>{
    // galleryStore.setPreviewImage(null);
  }



  onMount(async()=>{
    albums=await getAlbums();
  })
</script>

<div class="">
    <Title {title} {subtitle}/>
</div>


<div class="">
     <div class="gallery my-2" >
         <!-- Main Content -->
        <div class="gallery-content">
 
            <!-- Albums List -->
            <div class="albums-list">
                {#each albums as album (album.album_id)}
                <!-- svelte-ignore a11y_click_events_have_key_events -->
                <!-- svelte-ignore a11y_no_static_element_interactions -->
                <div class="" onclick={()=>{
                    selectedAlbumId=album.album_id
        ;          }}>
                    <AlbumCard {album} on:refresh={async(e)=>{
                    albums=await refreshAlbums();
                    }} />
                </div>
                {:else}
                <div class="no-albums">
                    <p>No albums yet.!</p>
                </div>
                {/each}
            </div>
        </div>
        
    </div>
</div>


<style>
  .gallery {
    min-height: 100vh;

  }
  
  .gallery-content {
    margin: 0 auto;
    padding: 10px;
    display: grid;
    gap: 20px;
    
  }
  
  @media (min-width: 768px) {
    .gallery-content {
      grid-template-columns: 1fr 1fr 1fr;
    }
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