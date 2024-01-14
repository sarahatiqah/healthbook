<!-- Modal -->
<div class="modal fade" id="append-modal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-image: linear-gradient(45deg, #29323c, #485563); color: #b8c7ce;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.2);">
                <h5 class="modal-title"><span class="card-title">Append Content</span></h5>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="content">Content</label>
                        <div class="input-group">
                            <textarea required type="text" class="form-control" name="content" id="content" placeholder="Enter content"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="border-top: 1px solid rgba(255, 255, 255, 0.2);">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="save">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>