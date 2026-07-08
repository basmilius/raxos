<template>
    <div class="link-cards">
        <a
            v-for="card in items"
            :key="card.link"
            class="link-card"
            :href="withBase(card.link)">
            <span class="link-card-title">
                <code v-if="card.code">{{ card.title }}</code>
                <template v-else>{{ card.title }}</template>
            </span>
            <span
                v-if="card.details"
                class="link-card-details">{{ card.details }}</span>
        </a>
    </div>
</template>

<script
    lang="ts"
    setup>
    import { computed } from 'vue';
    import { useData, withBase } from 'vitepress';

    interface Card {
        readonly title: string;
        readonly details?: string;
        readonly link: string;
        readonly code?: boolean;
    }

    const props = defineProps<{
        readonly group?: string;
    }>();

    const {frontmatter} = useData();

    const items = computed<Card[]>(() => {
        const cards = frontmatter.value.cards as Card[] | Record<string, Card[]> | undefined;

        if (!cards) {
            return [];
        }

        if (Array.isArray(cards)) {
            return cards;
        }

        return props.group ? cards[props.group] ?? [] : [];
    });
</script>

<style scoped>
    .link-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 12px;
        margin: 16px 0;
    }

    .link-card {
        display: flex;
        flex-direction: column;
        gap: 6px;
        padding: 16px 18px;
        border: 1px solid var(--vp-c-divider);
        border-radius: 12px;
        background: var(--vp-c-bg-soft);
        text-decoration: none;
        transition: border-color 0.15s, background 0.15s, transform 0.15s;
    }

    .link-card:hover {
        border-color: var(--vp-c-brand-1);
        background: var(--vp-c-bg-soft-up, var(--vp-c-bg-soft));
        transform: translateY(-2px);
    }

    .link-card-title {
        font-weight: 600;
        color: var(--vp-c-text-1);
    }

    .link-card-title code {
        padding: 2px 6px;
        border-radius: 6px;
        background: var(--vp-c-brand-soft);
        color: var(--vp-c-brand-1);
        font-family: var(--vp-font-family-mono);
        font-size: 0.85em;
    }

    .link-card-details {
        font-size: 0.875rem;
        line-height: 1.5;
        color: var(--vp-c-text-2);
    }
</style>
