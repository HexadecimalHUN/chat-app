// resources/assets/js/components/ChatUserHeader.vue

<template>
  <div class="chat-header clearfix" :class="isBlocked">
    <div class="row">
      <div class="col-9 d-flex">
        <a
          href="javascript:void(0);"
          data-toggle="modal"
          data-target="#view_info"
        >
          <img :src="selectedUser.avatar_url" alt="avatar" />
        </a>

        <div class="chat-about">
          <h6 class="m-b-0">{{ selectedUser.name }}</h6>
          <div class="status" v-if="selectedUser.online">
            <i
              class="fa fa-circle"
              :class="selectedUser.online ? 'online' : ''"
            ></i>
            online
          </div>
          <small v-else>
            Last seen:
            {{ moment().from(selectedUser.last_seen, true) }} ago.
          </small>
        </div>
      </div>
      <div
        class="col-3 hidden-sm text-right"
        v-if="roomData.blocked_by !== selectedUser.id"
      >
        <a
          @click.prevent="blockUser"
          v-if="!roomData.is_blocked"
          class="btn btn-outline-danger"
        >
          <i class="fas fa-user-slash"></i>
        </a>

        <a @click.prevent="unblockUser" v-else class="btn btn-outline-success">
          <i class="fas fa-user"></i>
        </a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["selectedUser", "user", "roomId", "roomData"],
  computed: {
    isBlocked() {
      if (this.roomData.is_blocked) {
        if (this.roomData.blocked_by !== this.user.id) return "blocked";
        else return "";
      } else {
        return "";
      }
    },
    showButton() {
      if (this.roomData.blocked_by == null) {
        return true;
      } else if (this.roomData.blocked_by !== this.selectedUser) {
        return true;
      } else if (this.roomData.is_blocked == false) {
        return false;
      }
    }
  },
  methods: {
    blockUser() {
      this.$set(this.roomData, "is_blocked", true);
      this.$set(this.roomData, "blocked_by", this.user.id);

      axios
        .put(`chat/${this.roomId}/block`)
        .then((response) => console.log(response.data));
    },
    unblockUser() {
      this.$set(this.roomData, "is_blocked", false);
      this.$set(this.roomData, "blocked_by", null);

      axios
        .put(`chat/${this.roomId}/unblock`)
        .then((response) => console.log(response.data));
    }
  }
};
</script>
