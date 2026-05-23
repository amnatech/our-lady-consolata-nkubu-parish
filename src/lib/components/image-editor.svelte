<script>
  import { beforeUpdate } from 'svelte';
  
  export let image = null;
  export let onSave = null;
  export let onCancel = null;
  
  let filters = {
    brightness: 100,
    contrast: 100,
    saturation: 100,
    blur: 0,
    hueRotate: 0
  };
  
  let crop = {
    x: 0,
    y: 0,
    width: 100,
    height: 100
  };
  
  let rotation = 0;
  let scale = 1;
  let textOverlays = [];
  let activeOverlay = null;
  let canvas = null;
  let ctx = null;
  let imageObj = new Image();
  
  const filterStyles = () => `
    filter: 
      brightness(${filters.brightness}%)
      contrast(${filters.contrast}%)
      saturate(${filters.saturation}%)
      blur(${filters.blur}px)
      hue-rotate(${filters.hueRotate}deg);
    transform: scale(${scale}) rotate(${rotation}deg);
  `;
  
  const cropStyles = () => `
    object-position: ${crop.x}% ${crop.y}%;
    object-fit: none;
    width: ${crop.width}%;
    height: ${crop.height}%;
  `;
  
  function handleFilterChange(e) {
    const { name, value } = e.target;
    filters = { ...filters, [name]: parseFloat(value) };
  }
  
  function handleCropChange(e) {
    const { name, value } = e.target;
    crop = { ...crop, [name]: parseFloat(value) };
  }
  
  function rotateImage(degrees) {
    rotation = (rotation + degrees) % 360;
  }
  
  function addTextOverlay() {
    const newOverlay = {
      id: Date.now(),
      text: 'Double click to edit',
      x: 50,
      y: 50,
      size: 24,
      color: '#ffffff',
      font: 'Arial'
    };
    textOverlays = [...textOverlays, newOverlay];
    activeOverlay = newOverlay.id;
  }
  
  function updateOverlay(id, updates) {
    textOverlays = textOverlays.map(overlay => 
      overlay.id === id ? { ...overlay, ...updates } : overlay
    );
  }
  
  function removeOverlay(id) {
    textOverlays = textOverlays.filter(overlay => overlay.id !== id);
    if (activeOverlay === id) activeOverlay = null;
  }
  
  function resetFilters() {
    filters = {
      brightness: 100,
      contrast: 100,
      saturation: 100,
      blur: 0,
      hueRotate: 0
    };
    crop = { x: 0, y: 0, width: 100, height: 100 };
    rotation = 0;
    scale = 1;
    textOverlays = [];
  }
  
  async function saveImage() {
    if (!image) return;
    
    // Create canvas for final image
    const tempCanvas = document.createElement('canvas');
    const tempCtx = tempCanvas.getContext('2d');
    
    // Load image
    await new Promise((resolve) => {
      imageObj.onload = resolve;
      imageObj.src = image;
    });
    
    // Set canvas dimensions
    tempCanvas.width = imageObj.width;
    tempCanvas.height = imageObj.height;
    
    // Apply filters to canvas
    tempCtx.filter = `
      brightness(${filters.brightness}%)
      contrast(${filters.contrast}%)
      saturate(${filters.saturation}%)
      blur(${filters.blur}px)
      hue-rotate(${filters.hueRotate}deg)
    `;
    
    // Apply rotation and scaling
    tempCtx.save();
    tempCtx.translate(tempCanvas.width / 2, tempCanvas.height / 2);
    tempCtx.rotate((rotation * Math.PI) / 180);
    tempCtx.scale(scale, scale);
    
    // Apply crop
    const cropX = (crop.x / 100) * imageObj.width;
    const cropY = (crop.y / 100) * imageObj.height;
    const cropWidth = (crop.width / 100) * imageObj.width;
    const cropHeight = (crop.height / 100) * imageObj.height;
    
    tempCtx.drawImage(
      imageObj,
      cropX, cropY, cropWidth, cropHeight,
      -cropWidth / 2, -cropHeight / 2, cropWidth, cropHeight
    );
    
    // Add text overlays
    textOverlays.forEach(overlay => {
      tempCtx.font = `${overlay.size}px ${overlay.font}`;
      tempCtx.fillStyle = overlay.color;
      tempCtx.textAlign = 'center';
      const x = overlay.x * tempCanvas.width / 100;
      const y = overlay.y * tempCanvas.height / 100;
      tempCtx.fillText(overlay.text, x, y);
    });
    
    tempCtx.restore();
    
    // Convert to blob
    tempCanvas.toBlob((blob) => {
      const editedImage = {
        url: URL.createObjectURL(blob),
        originalUrl: image,
        filters,
        crop,
        rotation,
        scale,
        textOverlays,
        metadata: {
          width: tempCanvas.width,
          height: tempCanvas.height,
          size: blob.size,
          type: blob.type
        }
      };
      
      if (onSave) onSave(editedImage);
    }, 'image/jpeg', 0.9);
  }
</script>

<div class="image-editor">
  {#if image}
    <div class="editor-container">
      <!-- Image Preview -->
      <div class="image-preview-container">
        <div class="image-wrapper" style="transform: rotate({rotation}deg) scale({scale});">
          <img
            src={image}
            alt="Editing preview"
            style="
              filter: 
                brightness({filters.brightness}%)
                contrast({filters.contrast}%)
                saturate({filters.saturation}%)
                blur({filters.blur}px)
                hue-rotate({filters.hueRotate}deg);
              object-position: {crop.x}% {crop.y}%;
              object-fit: none;
              width: {crop.width}%;
              height: {crop.height}%;
            "
          />
          {#each textOverlays as overlay (overlay.id)}
            <div
              class="text-overlay {activeOverlay === overlay.id ? 'active' : ''}"
              style="
                left: {overlay.x}%;
                top: {overlay.y}%;
                font-size: {overlay.size}px;
                color: {overlay.color};
                font-family: {overlay.font};
              "
              on:click={() => activeOverlay = overlay.id}
              on:dblclick={() => {
                const newText = prompt('Enter text:', overlay.text);
                if (newText !== null) updateOverlay(overlay.id, { text: newText });
              }}
              contenteditable={activeOverlay === overlay.id}
              on:blur={(e) => updateOverlay(overlay.id, { text: e.target.textContent })}
            >
              {overlay.text}
            </div>
          {/each}
        </div>
      </div>
      
      <!-- Controls -->
      <div class="controls">
        <!-- Filter Controls -->
        <div class="control-group">
          <h3>Filters</h3>
          <div class="slider-control">
            <label>Brightness</label>
            <input
              type="range"
              name="brightness"
              min="0"
              max="200"
              value={filters.brightness}
              on:input={handleFilterChange}
            />
            <span>{filters.brightness}%</span>
          </div>
          
          <div class="slider-control">
            <label>Contrast</label>
            <input
              type="range"
              name="contrast"
              min="0"
              max="200"
              value={filters.contrast}
              on:input={handleFilterChange}
            />
            <span>{filters.contrast}%</span>
          </div>
          
          <div class="slider-control">
            <label>Saturation</label>
            <input
              type="range"
              name="saturation"
              min="0"
              max="200"
              value={filters.saturation}
              on:input={handleFilterChange}
            />
            <span>{filters.saturation}%</span>
          </div>
          
          <div class="slider-control">
            <label>Blur</label>
            <input
              type="range"
              name="blur"
              min="0"
              max="20"
              step="0.1"
              value={filters.blur}
              on:input={handleFilterChange}
            />
            <span>{filters.blur}px</span>
          </div>
          
          <div class="slider-control">
            <label>Hue</label>
            <input
              type="range"
              name="hueRotate"
              min="0"
              max="360"
              value={filters.hueRotate}
              on:input={handleFilterChange}
            />
            <span>{filters.hueRotate}°</span>
          </div>
        </div>
        
        <!-- Crop Controls -->
        <div class="control-group">
          <h3>Crop</h3>
          <div class="slider-control">
            <label>X Position</label>
            <input
              type="range"
              name="x"
              min="0"
              max="100"
              value={crop.x}
              on:input={handleCropChange}
            />
            <span>{crop.x}%</span>
          </div>
          
          <div class="slider-control">
            <label>Y Position</label>
            <input
              type="range"
              name="y"
              min="0"
              max="100"
              value={crop.y}
              on:input={handleCropChange}
            />
            <span>{crop.y}%</span>
          </div>
          
          <div class="slider-control">
            <label>Width</label>
            <input
              type="range"
              name="width"
              min="10"
              max="100"
              value={crop.width}
              on:input={handleCropChange}
            />
            <span>{crop.width}%</span>
          </div>
          
          <div class="slider-control">
            <label>Height</label>
            <input
              type="range"
              name="height"
              min="10"
              max="100"
              value={crop.height}
              on:input={handleCropChange}
            />
            <span>{crop.height}%</span>
          </div>
        </div>
        
        <!-- Transform Controls -->
        <div class="control-group">
          <h3>Transform</h3>
          <div class="button-group">
            <button on:click={() => rotateImage(-90)}>↶ 90°</button>
            <button on:click={() => rotateImage(90)}>↷ 90°</button>
            <button on:click={() => rotateImage(180)}>180°</button>
          </div>
          
          <div class="slider-control">
            <label>Scale</label>
            <input
              type="range"
              min="0.1"
              max="3"
              step="0.1"
              bind:value={scale}
            />
            <span>{scale.toFixed(1)}x</span>
          </div>
        </div>
        
        <!-- Text Overlays -->
        <div class="control-group">
          <h3>Text Overlays</h3>
          <button on:click={addTextOverlay}>Add Text</button>
          {#if activeOverlay}
            <div class="overlay-controls">
              <div class="slider-control">
                <label>Font Size</label>
                <input
                  type="range"
                  min="12"
                  max="72"
                  value={textOverlays.find(o => o.id === activeOverlay)?.size || 24}
                  on:input={(e) => updateOverlay(activeOverlay, { size: e.target.value })}
                />
              </div>
              
              <div class="color-picker">
                <label>Color</label>
                <input
                  type="color"
                  value={textOverlays.find(o => o.id === activeOverlay)?.color || '#ffffff'}
                  on:input={(e) => updateOverlay(activeOverlay, { color: e.target.value })}
                />
              </div>
              
              <button on:click={() => removeOverlay(activeOverlay)}>
                Remove Overlay
              </button>
            </div>
          {/if}
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
          <button class="reset-btn" on:click={resetFilters}>Reset All</button>
          <button class="cancel-btn" on:click={onCancel}>Cancel</button>
          <button class="save-btn" on:click={saveImage}>Save Changes</button>
        </div>
      </div>
    </div>
  {:else}
    <div class="no-image">
      <p>No image selected for editing</p>
    </div>
  {/if}
</div>

<style>
  .image-editor {
    width: 100%;
    height: 100%;
    background: #1a1a1a;
    border-radius: 8px;
    overflow: hidden;
  }
  
  .editor-container {
    display: flex;
    height: 100%;
    gap: 20px;
  }
  
  .image-preview-container {
    flex: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #000;
    border-radius: 4px;
    overflow: hidden;
    position: relative;
  }
  
  .image-wrapper {
    position: relative;
    max-width: 100%;
    max-height: 100%;
  }
  
  .image-wrapper img {
    display: block;
    max-width: 100%;
    max-height: 80vh;
  }
  
  .text-overlay {
    position: absolute;
    transform: translate(-50%, -50%);
    cursor: move;
    padding: 4px 8px;
    user-select: none;
    white-space: nowrap;
  }
  
  .text-overlay.active {
    outline: 2px dashed #4CAF50;
    background: rgba(76, 175, 80, 0.1);
  }
  
  .controls {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #2d2d2d;
  }
  
  .control-group {
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #444;
  }
  
  .control-group h3 {
    margin: 0 0 16px 0;
    color: #fff;
    font-size: 16px;
  }
  
  .slider-control {
    margin-bottom: 12px;
  }
  
  .slider-control label {
    display: block;
    color: #ccc;
    font-size: 14px;
    margin-bottom: 4px;
  }
  
  .slider-control input[type="range"] {
    width: 100%;
    margin-bottom: 4px;
  }
  
  .slider-control span {
    display: block;
    text-align: right;
    color: #888;
    font-size: 12px;
  }
  
  .button-group {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
  }
  
  .button-group button {
    flex: 1;
    padding: 8px;
    background: #444;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .button-group button:hover {
    background: #555;
  }
  
  .color-picker {
    margin: 12px 0;
  }
  
  .color-picker label {
    display: block;
    color: #ccc;
    margin-bottom: 4px;
  }
  
  .action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 24px;
  }
  
  .action-buttons button {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
  }
  
  .reset-btn {
    background: #666;
    color: white;
  }
  
  .cancel-btn {
    background: #f44336;
    color: white;
  }
  
  .save-btn {
    background: #4CAF50;
    color: white;
  }
  
  .no-image {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 300px;
    color: #888;
  }
</style>