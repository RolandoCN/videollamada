<template>
  <div class="hello">
    <table ref="suppliersTable" class="table table-hover table-nomargin table-bordered dataTable">
        <thead>
            <tr>
                <th>Supplier #</th>
                <th>Company</th>
                <th class='hidden-1024'>Email</th>
                <th class='hidden-480'>Phone</th>
                <th class='hidden-480'>Fax</th>
                <th class='hidden-480'>Mobile</th>
                <th class='hidden-480'>City</th>
                <th class='hidden-480'>Street</th>
                <th class='hidden-480'>Postal Code</th>
                <th class='hidden-480'>CR</th>
                <th class='hidden-480'>Website</th>
                <th class='hidden-480'>Notes</th>
            </tr>
        </thead>
        <tbody>                            
            <tr class="template" v-for="supplier in suppliers" :key="supplier.Supplier_ID">
                <td class='verticalAlignMiddle'>{{ supplier.Supplier_ID }}</td>
                <td class='verticalAlignMiddle'>{{ supplier.Company }}</td>
                <td class='verticalAlignMiddle hidden-1024'>{{ supplier.Email }}</td>
                <td class='verticalAlignMiddle hidden-480'>{{ supplier.Phone }}</td>
                <td class='verticalAlignMiddle hidden-480'>{{ supplier.Fax }}</td>
                <td class='verticalAlignMiddle hidden-480'>{{ supplier.Mobile }}</td>
                <td class='verticalAlignMiddle hidden-480'>{{ supplier.City_ID }}</td>
                <td class='verticalAlignMiddle hidden-480'>{{ supplier.Street }}</td>
                <td class='verticalAlignMiddle hidden-480'>{{ supplier.Postal_Code }}</td>
                <td class='verticalAlignMiddle hidden-480'>{{ supplier.CR }}</td>
                <td class='verticalAlignMiddle hidden-480'>{{ supplier.Website }}</td>
                <td class='verticalAlignMiddle hidden-480'>{{ supplier.Notes }}</td>
            </tr>
        </tbody>
    </table>
  </div>
</template>

<script>
// import faker from 'faker';
// const $ = require('jquery');
// const dt = require('datatables.net')();

import * as $ from 'jquery'
import dt from 'datatables.net-bs4'
$.fn.DataTable = dt

export default {
  // name: "HelloWorld",
  data() {
    return {
      suppliers: []
    };
  },
  mounted() {
    this.dt = $(this.$refs.suppliersTable).DataTable();
    setTimeout(() => this.suppliers = fakeData(50), 1000);
  },
  watch: {
    suppliers(val) {
      this.dt.destroy();
      this.$nextTick(() => {
        this.dt = $(this.$refs.suppliersTable).DataTable()
      })
    }
  },
};

function fakeData(count) {
  return new Array(count).fill().map(x => ({
    Supplier_ID: faker.random.number(),
    Company: faker.company.companyName(),
    Email: faker.internet.email(),
    Phone: faker.phone.phoneNumber(),
    Fax: faker.phone.phoneNumber(),
    Mobile: faker.phone.phoneNumber(),
    City_ID: faker.address.city(),
    Street: faker.address.streetAddress(),
    Postal_Code: faker.address.zipCode(),
    CR: faker.random.boolean(),
    Website: faker.internet.url(),
    Notes: faker.lorem.words(),
  }));
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<!-- <style src="./jquery.datatables.css"/> -->
