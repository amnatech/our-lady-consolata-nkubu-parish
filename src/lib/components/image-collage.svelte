<script>
  import { onMount } from 'svelte';
  import ImageTile from '$lib/components/image-tile.svelte';
  
  export let images = [];
  export let rows = 3;
  export let columns = 4;
  export let gap = 10;
  export let borderRadius = 8;
  export let mosaicStyle="grid";
  export let showControls = true;
  
  let containerRef;
  let selectedImage = null;
  let zoomed = false;
  
  // Mosaic layout patterns
  const layoutPatterns = {
    grid: Array.from({ length: rows * columns }, (_, i) => ({
      width: 100 / columns,
      height: 100 / rows,
      left: (i % columns) * (100 / columns),
      top: Math.floor(i / columns) * (100 / rows)
    })),
    
    random: Array.from({ length: rows * columns }, (_, i) => ({
      width: 20 + Math.random() * 30, // 20-50%
      height: 20 + Math.random() * 30,
      left: Math.random() * (100 - 30),
      top: Math.random() * (100 - 30)
    })),
    
    pyramid: Array.from({ length: rows * columns }, (_, i) => {
      const row = Math.floor(i / columns);
      const col = i % columns;
      const size = 100 / (row + 2);
      return {
        width: size,
        height: size,
        left: col * (100 / columns),
        top: row * (100 / rows)
      };
    }),
    
    circular: Array.from({ length: rows * columns }, (_, i) => {
      const angle = (i / (rows * columns)) * 2 * Math.PI;
      const radius = 40;
      const size = 15 + Math.random() * 15;
      return {
        width: size,
        height: size,
        left: 50 + radius * Math.cos(angle) - size / 2,
        top: 50 + radius * Math.sin(angle) - size / 2
      };
    })
  };
  
  let layout = layoutPatterns[mosaicStyle];
  
  // Update layout when style changes
  $: if (images.length > 0) {
    layout = layoutPatterns[mosaicStyle];
  }
  
  // Handle image click
  const handleImageClick = (image) => {
    selectedImage = image;
    zoomed = true;
    document.body.style.overflow = 'hidden';
  };
  
  // Close zoom view
  const closeZoom = () => {
    selectedImage = null;
    zoomed = false;
    document.body.style.overflow = '';
  };
  
  // Handle keyboard events
  const handleKeydown = (e) => {
    if (e.key === 'Escape' && zoomed) {
      closeZoom();
    }
  };
  
  onMount(() => {
   console.log(images);
  });
</script>

<svelte:head>
  <style>
    :global(.collage-zoomed) {
      overflow: hidden;
    }
  </style>
</svelte:head>

<div class="collage-container">
  {#if showControls}
    <div class="controls">
      <select bind:value={mosaicStyle}>
        <option value="grid">Grid</option>
        <option value="random">Random</option>
        <option value="pyramid">Pyramid</option>
        <option value="circular">Circular</option>
      </select>
      <div class="slider-group">
        <label>Rows: {rows}</label>
        <input type="range" min="1" max="8" bind:value={rows}>
      </div>
      <div class="slider-group">
        <label>Columns: {columns}</label>
        <input type="range" min="1" max="8" bind:value={columns}>
      </div>
    </div>
  {/if}
  
  <div 
    class="mosaic" 
    style="
      --gap: {gap}px;
      --border-radius: {borderRadius}px;
    "
  >
    {#each images as image, i}
      {#if i < rows * columns}
        <ImageTile
          {image}
          {borderRadius}
          style={`
            width: ${layout[i].width}%;
            height: ${layout[i].height}%;
            left: ${layout[i].left}%;
            top: ${layout[i].top}%;
          `}
          on:click={() => handleImageClick(image)}
          animate={true}
        />
      {/if}
    {/each}
  </div>
  

</div>

<style>
  .collage-container {
    position: relative;
    width: 100%;
    height: 600px;
    margin: 0 auto;
  }

  .controls {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    padding: 15px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    align-items: center;
    flex-wrap: wrap;
  }

  select {
    padding: 8px 16px;
    border-radius: 6px;
    border: 2px solid #e2e8f0;
    background: white;
    font-size: 14px;
    cursor: pointer;
    transition: border-color 0.3s;
  }

  select:hover {
    border-color: #4299e1;
  }

  .slider-group {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .slider-group label {
    font-size: 14px;
    font-weight: 500;
    color: #4a5568;
    min-width: 80px;
  }

  input[type="range"] {
    width: 120px;
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    outline: none;
    -webkit-appearance: none;
  }

  input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 18px;
    height: 18px;
    background: #4299e1;
    border-radius: 50%;
    cursor: pointer;
    transition: transform 0.2s;
  }

  input[type="range"]::-webkit-slider-thumb:hover {
    transform: scale(1.2);
  }

  .mosaic {
    position: relative;
    width: 100%;
    height: calc(100% - 80px);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  }

  .zoom-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(10px);
    z-index: 1000;
    display: flex;
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.3s ease;
  }

  .zoom-content {
    position: relative;
    width: 90vw;
    height: 90vh;
    max-width: 1200px;
    max-height: 800px;
    background: transparent;
  }

  .close-btn {
    position: absolute;
    top: -40px;
    right: 0;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    font-size: 28px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    z-index: 1001;
  }

  .close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
    }
    to {
      opacity: 1;
    }
  }

  @media (max-width: 768px) {
    .collage-container {
      height: 400px;
    }
    
    .controls {
      flex-direction: column;
      align-items: stretch;
    }
    
    .zoom-content {
      width: 95vw;
      height: 70vh;
    }
  }
</style>