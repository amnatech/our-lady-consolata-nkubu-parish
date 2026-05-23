<script>
  import ImageCollage from '$lib/components/image-collage.svelte';
	import { API_BASE_URL } from '$lib/config/base_urls';
	import { fetch_resource } from '$lib/methods/functions';
	import { onMount } from 'svelte';
  
  // Sample images (replace with your actual image URLs)
  let imageList = [];


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

  const getImages=(albums)=>{
    const imagesArrays=albums.map((a)=>a.images)

    let images=[];

    imagesArrays.forEach(arr => {
        for (let i = 0; i < arr.length; i++) {
            images.push(arr[i]);
            
        }
    });

    return images;
  }


  onMount(async()=>{

    const albums=await getAlbums();

    imageList=getImages(albums);

    imageList=imageList
 
  })
</script>

<main class="p-5">
    <div class="py-5">
        <div class="title text-2xl text-center font-bold mt-2">
        <span class="text-wekebio-red">Photo</span> <span class="text-consolata-blue">Gallery</span>
    </div>
    <div class="hline border-t-6 w-18 m-auto border-wekebio-purple mb-2">
        <br>
    </div>
    </div>
  {#if imageList.length>0}
    <div class="grid grid-cols-4 gap-3">
        {#each imageList as image}
            <div class="flex-1">
                <img src="{API_BASE_URL}{image}" alt="{image}">
            </div>
        {/each}
    </div>
  {/if}
  
 

</main>

<style>
    main{
        background: rgba(255, 255, 255, 0.1);
    }

  @media (max-width: 768px) {

  }
</style>