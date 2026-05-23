<script>

    export let organisation;

</script>
<main>
    <div class="flex justify-between gap-1 items-center align-middle pt-3">
        <div class="icon text-xl">
           <i class="ri-map-pin-line"></i>
        </div>
        <div class="dets text-sm">
            <div class="address">
                P.O Box  {organisation.address.postal_code} vvv
            </div>

            <div class="email">
              {organisation.address.constituency}, {organisation.address.county}
            </div>
        </div>
    </div>
</main>