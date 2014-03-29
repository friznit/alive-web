<script type="text/javascript">
    $(document).ready(function() {
        $(window).on("resize", function() {
            $("#map").height($(window).height()).width($(window).width());
            map.invalidateSize();
        }).trigger("resize");
    });
</script>
