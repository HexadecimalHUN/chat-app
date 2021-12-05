// resources/assets/js/components/MessageActions.vue

<template>
  <div class="action-box align-self-center px-3" v-if="!message.is_removed">
    <i
      class="fas fa-trash-alt m-1"
      @click.prevent="deleteMessage(message.id)"
    ></i>
    <i class="fas fa-thumbtack m-1" @click.prevent="pinMessage(message.id)"></i>
  </div>
</template>

<script>
export default {
  props: ["message", "roomId"],
  methods: {
    deleteMessage(messageId) {
      this.$set(this.message, "is_removed", true);

      axios
        .put(`chat/${this.roomId}/remove-message/${messageId}`)
        .then((response) => console.log(response.data));
    },
    pinMessage(messageId) {
      this.$set(this.message, "is_pinned", true);

      axios
        .put(`chat/${this.roomId}/pin-message/${messageId}`)
        .then((response) => console.log(response.data));
    }
  }
};
</script>