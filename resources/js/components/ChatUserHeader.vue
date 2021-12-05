// resources/assets/js/components/ChatUserHeader.vue

<template>
  <div class="chat-header clearfix">
    <div class="row">
      <div class="col-lg-6">
        <a
          href="javascript:void(0);"
          data-toggle="modal"
          data-target="#view_info"
        >
          <img :src="user.avatar_url" alt="avatar" />
        </a>

        <div class="chat-about">
          <h6 class="m-b-0">{{ user.name }}</h6>
          <div class="status" v-if="user.online">
            <i class="fa fa-circle" :class="user.online ? 'online' : ''"></i>
            online
          </div>
          <small v-else>
            Last seen:
            {{ moment().from(user.last_seen, true) }} ago.
          </small>
        </div>

        <div class="col-lg-6 hidden-sm text-right">
          <a  @click.prevent="blockUser" class="btn btn-outline-danger">
            <i class="fas fa-user-slash"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["user"]
};
</script>
