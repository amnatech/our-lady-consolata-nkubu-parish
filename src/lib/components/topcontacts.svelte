<script>
    export let organisation;

</script>
<main>
    <div class="flex justify-between gap-1 items-center align-middle pt-3">
        <div class="icon text-xl">
            <i class="ri-phone-line"></i>
        </div>
        <div class="dets text-sm ">
            <div class="phone">
                <a href="tel:{organisation.phone}">+{organisation.phone}</a>
            </div>

            <div class="email">
                <a href="mail:{organisation.email}">{organisation.email}</a>
            </div>
        </div>
    </div>
</main>