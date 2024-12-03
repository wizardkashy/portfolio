`timescale 1ns / 1ps
//////////////////////////////////////////////////////////////////////////////////
// Company: 
// Engineer: 
// 
// Create Date: 05/15/2024 12:14:33 PM
// Design Name: 
// Module Name: RegFile
// Project Name: 
// Target Devices: 
// Tool Versions: 
// Description: 
// 
// Dependencies: 
// 
// Revision:
// Revision 0.01 - File Created
// Additional Comments:
// 
//////////////////////////////////////////////////////////////////////////////////


module RegFile(
    input clk,
    input reset,
    input rg_wrt_en,
    input [4:0] rg_rd_addr1,
    input [4:0] rg_rd_addr2,
    input [4:0] rg_wrt_addr,
    input [31:0] rg_wrt_data,
    output [31:0] rg_rd_data1,
    output [31:0] rg_rd_data2
    );
    reg [31:0] regfile [31:0];
    initial
    begin
            regfile[0] <= 32'h0;
            regfile[1] <= 32'h0;
            regfile[2] <= 32'h0;
            regfile[3] <= 32'h0;
            regfile[4] <= 32'h0;
            regfile[5] <= 32'h0;
            regfile[6] <= 32'h0;
            regfile[7] <= 32'h0;
            regfile[8] <= 32'h0;
            regfile[9] <= 32'h0;
            regfile[10] <= 32'h0;
            regfile[11] <= 32'h0;
            regfile[12] <= 32'h0;
            regfile[13] <= 32'h0;
            regfile[14] <= 32'h0;
            regfile[15] <= 32'h0;
            regfile[16] <= 32'h0;
            regfile[17] <= 32'h0;
            regfile[18] <= 32'h0;
            regfile[19] <= 32'h0;
            regfile[20] <= 32'h0;
            regfile[21] <= 32'h0;
            regfile[22] <= 32'h0;
            regfile[23] <= 32'h0;
            regfile[24] <= 32'h0;
            regfile[25] <= 32'h0;
            regfile[26] <= 32'h0;
            regfile[27] <= 32'h0;
            regfile[28] <= 32'h0;
            regfile[29] <= 32'h0;
            regfile[30] <= 32'h0;
            regfile[31] <= 32'h0;
    end
    
    always @(posedge reset, posedge clk)
    begin
        if (reset == 1'b1)
        begin
            regfile[0] <= 32'h0;
            regfile[1] <= 32'h0;
            regfile[2] <= 32'h0;
            regfile[3] <= 32'h0;
            regfile[4] <= 32'h0;
            regfile[5] <= 32'h0;
            regfile[6] <= 32'h0;
            regfile[7] <= 32'h0;
            regfile[8] <= 32'h0;
            regfile[9] <= 32'h0;
            regfile[10] <= 32'h0;
            regfile[11] <= 32'h0;
            regfile[12] <= 32'h0;
            regfile[13] <= 32'h0;
            regfile[14] <= 32'h0;
            regfile[15] <= 32'h0;
            regfile[16] <= 32'h0;
            regfile[17] <= 32'h0;
            regfile[18] <= 32'h0;
            regfile[19] <= 32'h0;
            regfile[20] <= 32'h0;
            regfile[21] <= 32'h0;
            regfile[22] <= 32'h0;
            regfile[23] <= 32'h0;
            regfile[24] <= 32'h0;
            regfile[25] <= 32'h0;
            regfile[26] <= 32'h0;
            regfile[27] <= 32'h0;
            regfile[28] <= 32'h0;
            regfile[29] <= 32'h0;
            regfile[30] <= 32'h0;
            regfile[31] <= 32'h0;
        end
        if (rg_wrt_en == 1'b1)
        begin
            regfile[rg_wrt_addr] = rg_wrt_data;
        end
    end
    assign rg_rd_data1 = regfile[rg_rd_addr1];
    assign rg_rd_data2 = regfile[rg_rd_addr2];
endmodule
