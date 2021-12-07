// resources/assets/js/components/PinnedMessage.vue

<template>
  <div class="chat-header clearfix">
    <!-- v-for="pin in pinned" :key="pin.id" -->
    <div class="row">
      <div class="col-10 d-flex">
        <div class="chat-about">
          <small> {{ pinned[0].message }} </small>
        </div>
      </div>

      <div class="col-2 hidden-sm text-right">
        <a @click.prevent="removeMessage" class="text-muted">
          <i class="fas fa-close"></i>
        </a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["pinned"],
  methods: {
    async removeMessage() {
      await axios
        .delete(`/chat/${this.pinned[0].chat_room_id}/delete-pinned`)
        .then((response) => this.$set(this.pinned, 0, ""));
    }
  }
};
</script>