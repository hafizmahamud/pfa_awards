<script>
  export default {
    props: {
        documentUpload: Object,
    },
    data: function () {
            return {
            };
    },
    name: 'Modal',
    setup(props){
        console.log(props.documentUpload);
    },
    methods: {
      close() {
        this.$emit('close');
      },
    },
  };
  
</script>

<template>
  
    <div class="modal">
      <header class="modal-header">
        <slot name="header">
          Documents Uploaded
        </slot>
        <button
          type="button"
          class="btn-close"
          @click="close"
        >
          x
        </button>
      </header>

      <section class="modal-body">
        <table class="table table-striped">
            <!--Table head-->
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>View</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tr v-for="documentUploads in documentUpload" :key="documentUploads.id">
            <td>{{ documentUploads.unique_file_name }} {{ documentUploads.document_collection_id }}</td>
            <td><a :href="'/storage/documents/' + documentUploads.unique_file_name" target="_blank">View</a></td>
            <td><a :href="'/storage/documents/' + documentUploads.id" class="rounded-lg bg-gray-200 px-4 py-1">
            Download
                </a>
            </td>
            </tr>
        </table>
       </section>

      <footer class="modal-footer">
        <button
          type="button"
          class="btn-green"
          @click="close"
        >
          Close Modal
        </button>
      </footer>
    </div>

</template>

<style>
  .modal-backdrop {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.3);
    display: fixed;
    justify-content: center;
    align-items: center;
  }

  .modal {
    width: 25%;
    background: #FFFFFF;
    box-shadow: 2px 2px 20px 1px;
    overflow-x: auto;
    display: flex;
    flex-direction: column;
  }
 
  .modal-header,
  .modal-footer {
    padding: 15px;
    display: flex;
  }

  .modal-header {
    position: relative;
    border-bottom: 1px solid #eeeeee;
    color: #4AAE9B;
    justify-content: space-between;
  }

  .modal-footer {
    border-top: 1px solid #eeeeee;
    flex-direction: column;
    justify-content: flex-end;
  }

  .modal-body {
    position: relative;
    padding: 20px 10px;
  }

  .btn-close {
    position: absolute;
    top: 0;
    right: 0;
    border: none;
    font-size: 20px;
    padding: 10px;
    cursor: pointer;
    font-weight: bold;
    color: #4AAE9B;
    background: transparent;
  }

  .btn-green {
    color: white;
    background: #4AAE9B;
    border: 1px solid #4AAE9B;
    border-radius: 2px;
  }

  td {
  height: 10px;
  vertical-align: bottom;
  padding: 10px;
}
</style>