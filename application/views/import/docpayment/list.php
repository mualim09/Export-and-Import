<div class="card">
    <div class="card-header">
        <h6><i class="fas fa-list-alt mr-2"></i><?=$header?></h6>
    </div>
    <div class="card-body">
        <table id="tdocpaymentlist" class="table table-sm table-bordered table-striped">
            <thead>
                <tr class="text-center">
                    <th>#</th>
                    <th>Code</th>
                    <th>Shipper</th>
                    <th>PO #</th>
                    <th>Consignee</th>
                    <th>Commodity</th>
                    <th>BL no.</th>
                    <th>Estimasi</th>
                    <th>Aktual total bayar di PIB</th>
                    <th>Created at</th>
                    <th><i class="fas fa-ellipsis-h"></i></th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($params['list'] as $rows) : ?>
                    <tr class="align-middle text-center">
                        <td><?=$no?>.</td>
                        <td><?=$rows->code?></td>
                        <td><?=$rows->shipper?></td>
                        <td><?=$rows->po_no?></td>
                        <td><?=$rows->consignee?></td>
                        <td><?=$rows->commodity?></td>
                        <td><?=$rows->mbl?></td>
                        <td><?=number_format($rows->estimasi, 2)?></td>
                        <td><?=number_format($rows->actual)?></td>
                        <td><?=$rows->created_at?></td>
                        <td>
                            <!-- <a href="<?=site_url('import/docimport/detail/'.$rows->id)?>" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a> -->
                            <button class="btn btn-sm btn-danger" id="delete" data-id="<?=$rows->id?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php $no++; endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <div class="float-right">
            <small>Page rendered in <strong>{elapsed_time}</strong> seconds.</small>
        </div>
    </div>
</div>