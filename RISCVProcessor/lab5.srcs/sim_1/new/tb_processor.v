`timescale 1ns / 1ps
//////////////////////////////////////////////////////////////////////////////////
// Company: 
// Engineer: 
// 
// Create Date: 06/03/2024 11:01:40 PM
// Design Name: 
// Module Name: tb_processor
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


module tb_processor();
    reg clk, rst;
    wire [31:0] tb_Result;
    processor processor_inst(clk, rst, tb_Result);
    always
    begin
        #10;
        clk = ~clk;
    end
    initial
    begin
    clk = 0;
    @(posedge clk); rst = 1;
    @(posedge clk); rst = 0;
    end
    
    initial begin
        $dumpfile("test.vcd"); $dumpvars;
    end
    
    integer point = 0;
    always @(*)
    begin
        #20;
        if (tb_Result == 32'h00000000)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'h00000001)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'h00000002)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'h00000004)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'h00000005)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'h00000007)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'h00000008)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'h0000000b)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'h00000003)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'hfffffffe)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'h00000000)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'h00000005)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'h00000001)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'hfffffff4)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'h000004D2)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'hfffff8d7)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'h00000001)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'hfffffb2c)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'h00000030)
        begin
            point = point + 1;
        end
        
        #20;
        if (tb_Result == 32'h00000030)
        begin
            point = point + 1;
        end
        
        $display("%s%d","The number of correct test cases is :", point);
    end
    
    initial begin
        #430;
    end
endmodule
