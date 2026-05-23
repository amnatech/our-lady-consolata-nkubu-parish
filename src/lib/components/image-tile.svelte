<script>
	import { API_BASE_URL } from '$lib/config/base_urls';
  import { onMount } from 'svelte';

  
  export let image;
  export let style = '';
  export let borderRadius = 8;
  export let zoomable = false;
  export let fullscreen = false;
  export let animate = true;
  
  let containerRef;
  let imgRef;
  let loaded = true;
  let scale = 1;
  
  onMount(() => {
    console.log(image);
  });
  

  
  const handleLoad = () => {
    loaded = true;
  };
  

</script>

<!-- svelte-ignore a11y_no_static_element_interactions -->


     {#if image}
       <img
      src={API_BASE_URL+image};
      alt={image}
      style="border-radius: {borderRadius}px;"
    />

      
     {/if}

  



<style>
  .image-tile {
    position: absolute;
    cursor: pointer;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    background: rgb(183, 71, 71);
  }

  .image-tile.animate {
    animation: floatIn 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .image-tile:hover:not(.fullscreen) {
    transform: scale(1.05);
    z-index: 10;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
  }

  .image-tile.zoomable {
    cursor: zoom-in;
  }

  .image-tile.fullscreen {
    width: 100% !important;
    height: 100% !important;
    left: 0 !important;
    top: 0 !important;
    cursor: grab;
    border-radius: 0;
    box-shadow: none;
  }

  .image-tile.fullscreen:active {
    cursor: grabbing;
  }

  .image-container {
    width: 100%;
    height: 100%;
    position: relative;
  }

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity 0.3s;
    opacity: 1;
    display: block;
  }

  img.loaded {
    opacity: 1;
  }

  .loading {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
  }

  .loading.hidden {
    display: none;
  }

  .spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #4299e1;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  .zoom-controls {
    position: absolute;
    bottom: 20px;
    right: 20px;
    display: flex;
    gap: 10px;
    z-index: 10;
  }

  .zoom-controls button {
    padding: 10px 15px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 6px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    transition: all 0.3s;
  }

  .zoom-controls button:hover {
    background: white;
    transform: translateY(-2px);
  }

  @keyframes floatIn {
    from {
      opacity: 0;
      transform: translateY(20px) scale(0.9);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  @keyframes loading {
    0% {
      background-position: 200% 0;
    }
    100% {
      background-position: -200% 0;
    }
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
</style>