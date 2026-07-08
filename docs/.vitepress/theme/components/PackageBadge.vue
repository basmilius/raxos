<template>
    <a
        class="package-badge"
        :href="`https://packagist.org/packages/raxos/${name}`"
        target="_blank"
        rel="noopener noreferrer">
        raxos/{{ name }}
    </a>
</template>

<script
    lang="ts"
    setup>
    defineProps<{
        readonly name: string;
    }>();
</script>
