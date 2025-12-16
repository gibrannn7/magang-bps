<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= $title ?></h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Data ini digunakan untuk Auto-Complete pada form pendaftaran.
        </div>
        <ul class="list-group">
            <?php foreach($list as $item): ?>
                <li class="list-group-item"><?= $item ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>