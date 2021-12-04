// resources/assets/js/components/ChatForm.vue

<template>
  <div class="chat-message clearfix">
    <div class="input-group mb-0">
      <a class="input-group-prepend" @click.prevent="sendMessage">
        <span class="input-group-text"><i class="fa fa-send"></i></span>
      </a>
      <input
        type="text"
        class="form-control"
        placeholder="Enter text here..."
        v-model="newMessage"
        @keyup.enter.prevent="sendMessage"
      />
    </div>
  </div>
</template>

<script>
export default {
  props: ["user", "sessionId"],

  data() {
    return {
      newMessage: ""
    };
  },
  // TODO: FIX TYPING INDICATOR HERE TOO
  watch: {
    newMessage(value) {
      if (value) {
        Echo.private(`private-chat.${this.sessionId}`).whisper("typing", {
          name: this.user.name
        });
      }
    }
  },
  methods: {
    sendMessage() {
      if (this.newMessage) {
        this.$emit("messagesent", {
          user_id: this.user.id,
          message: this.newMessage
        });

        this.newMessage = "";
      }
    }
  }
};
</script>